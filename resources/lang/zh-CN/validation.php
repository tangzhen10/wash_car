<?php

return [
	'required'             => ':attr不得为空！',
	'has_been_registered'  => '此:attr已被注册！',
	'not_registered'       => '此:attr尚未注册！',
	'has_exist'            => '此:attr已存在！',
	'min'                  => [
		'string' => ':attr至少:min个字符！',
		'number' => ':attr不得小于:min！',
	],
	'more_than'            => [
		'string' => ':attr必须大于:min个字符！',
		'number' => ':attr必须大于:min！',
	],
	'invalid'              => '无效的:attr！',
	'wrong'                => ':attr不正确，请重新输入！',
	'no_one_selected'      => '未选择任何条目！',
	'must_be_positive_int' => ':attr必须为正整数！',
];