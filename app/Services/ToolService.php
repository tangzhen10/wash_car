<?php

/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-28 0028 14:23
 */

namespace App\Services;

class ToolService {
	
	private $_validSMSCodeUseTypes = [
		'register', # 注册
		'bind_phone', # 绑定手机号码
	];
	
	public function sendSMSCode() {
		
		$this->_validation();
		
		# todo lxt 发送验证码 & 根据使用用途，在redis里生成有效期的验证码
	}
	
	public static function uploadFiles($files) {
		
		$file = $files;
		
		# 判断文件上传过程中是否出错
		if (!$file->isValid()) die(json_encode(['error' => '文件上传出错！']));
		
		$type    = $file->getClientOriginalExtension();
		$typeArr = config('project.ALLOW_PICTURE_TYPE');
		if (!in_array($type, $typeArr)) die(json_encode(['error' => '请上传jpg,jpeg,png或gif类型的图片！']));
		
		$size = $file->getClientSize();
		if ($size > config('project.MAX_SIZE_UPLOAD_FILE')) die(json_encode(['error' => '图片大小已超过限制！']));
		
		# 存盘目录
		$uploadPath        = config('project.UPLOAD_STORAGE_PATH').date('/Ymd/'); # 上传目录
		$uploadStoragePath = public_path($uploadPath); # 上传目录绝对路径
		if (!file_exists($uploadStoragePath)) @mkdir($uploadStoragePath, 0755, true);
		
		# 转移临时文件到存盘目录
		# $originalName = $file->getClientOriginalName();
		$fileName     = time().rand(11392, 92192).'.'.$type;
		if (!$file->move($uploadStoragePath, $fileName)) die(json_encode(['error' => '保存文件失败！']));
		
		# 缩略图
		create_thumb($uploadStoragePath.$fileName, false);
		
		return $uploadPath.$fileName;
	}
	
	/**
	 * 验证手机号是否可用
	 * @author 李小同
	 * @date   2018-6-29 18:13:37
	 * @return bool
	 */
	private function _validation() {
		
		$phone   = trim(\Request::input('phone'));
		$useType = \Request::input('use_type'); # 短信用途
		
		# 验证手机号码格式
		if (!preg_match(config('project.PATTERN.PHONE'), $phone)) {
			json_msg(trans('validation.invalid', ['attr' => trans('common.phone')]), 40003);
		}
		
		# 验证是否是合法的使用用途
		if (!in_array($useType, $this->_validSMSCodeUseTypes)) json_msg(trans('error.illegal_param'), 40003);
		
		switch ($useType) {
			case 'register' :
				# 验证手机号是否被注册
				\UserService::checkExistIdentity('phone', $phone);
				break;
			case 'bind_phone':
				# todo lxt
				break;
		}
		
		return true;
	}
}