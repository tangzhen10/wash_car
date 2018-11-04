<?php

$signature = $_GET['signature'];
$timestamp = $_GET['timestamp'];
$nonce     = $_GET['nonce'];
$token     = env('WECHAT_CHECK_TOKEN');
$tmpArr    = [$token, $timestamp, $nonce];
sort($tmpArr, SORT_STRING);
$tmpStr = implode($tmpArr);
$tmpStr = sha1($tmpStr);

if ($tmpStr == $signature) {
	echo $_GET['echostr'];
}

