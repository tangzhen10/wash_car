<?php

/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-28 0028 14:23
 */

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ToolService {
	
	private $_validSMSCodeUseTypes = [
		'login', # 登录
		'register', # 注册
		'login_by_phone', # 手机号登录
	];
	
	/**
	 * 发送短信验证码
	 * @author 李小同
	 * @date   2018-7-28 13:41:50
	 * @return bool
	 */
	public function sendSMSCode() {
		
		$phoneInfo = $this->_validation();
		
		$code = $this->createVerifyCode($phoneInfo);
		
		# todo lxt sendCode
		$res = '本次验证码为：'.$code;
		
		return $res;
	}
	
	/**
	 * 创建验证码
	 * @param array $phoneInfo
	 * @author 李小同
	 * @date   2018-7-28 13:42:07
	 * @return string
	 */
	public function createVerifyCode(array $phoneInfo) {
		
		$code = '';
		for ($i = 0; $i < $phoneInfo['codeLength']; $i++) $code .= rand(0, 9);
		
		$phone    = $phoneInfo['phone'];
		$useType  = $phoneInfo['useType'];
		$cacheKey = sprintf(config('cache.VERIFY_CODE.'.strtoupper($useType)), $phone);
		redisSet($cacheKey, $code);
		
		return $code;
	}
	
	/**
	 * 获取验证码
	 * @param array $phoneInfo
	 * @author 李小同
	 * @date   2018-7-28 15:31:06
	 * @return bool|mixed
	 */
	public function getVerifyCodeCacheKey(array $phoneInfo) {
		
		$phone    = $phoneInfo['phone'];
		$useType  = $phoneInfo['useType'];
		$cacheKey = sprintf(config('cache.VERIFY_CODE.'.strtoupper($useType)), $phone);
		
		return $cacheKey;
	}
	
	/**
	 * 上传文件
	 * @param $files Request::file()
	 * @author 李小同
	 * @date   2018-7-15 11:17:38
	 * @return string
	 */
	public static function uploadFiles($files) {
		
		# 单图按多图的格式处理 李小同 2018-8-2 17:03:58
		if (!is_array($files) && is_a($files, UploadedFile::class)) $files = [$files];
		
		$uploadedFiles = [];
		foreach ($files as $file) {
			
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
			$fileName = time().rand(11392, 92192).'.'.$type;
			if (!$file->move($uploadStoragePath, $fileName)) die(json_encode(['error' => '保存文件失败！']));
			
			# 缩略图
			create_thumb($uploadStoragePath.$fileName);
			
			$uploadedFiles[] = $uploadPath.$fileName;
		}
		
		return implode(',', $uploadedFiles);
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
		
		$codeLength = 6;
		switch ($useType) {
			case 'register' :
				# 验证手机号是否被注册
				$res = \UserService::checkExistIdentity('phone', $phone);
				if ($res) json_msg(trans('validation.has_been_registered', ['attr' => trans('common.phone')]), 40002);
				break;
			case 'login':
				# 验证手机号是否已注册
				$res = \UserService::checkExistIdentity('phone', $phone);
				if (!$res) json_msg(trans('validation.not_registered', ['attr' => trans('common.phone')]), 50001);
				break;
			case 'login_by_phone':
				# 手机号登录（未注册则自动注册）
				break;
		}
		
		return compact('phone', 'useType', 'codeLength');
	}
}