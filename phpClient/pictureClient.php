<?php

include('include.php');

if(!isset($argv[1]) || ($argv[1]!='show' && $argv[1]!='get'))
	die("Provide action! get, show");
if(!isset($argv[2]))
	die("Provide ID!");
if(!isset($argv[3]))
	die("Provide secret!");
if(!isset($argv[4]) && ($argv[1]=='show'))
	die("Provide other user ID!");

$time = time();

$url = 'http://localhost/wuersch/backend/picture';
if($argv[1] == 'show'){
	$toHash = $time . "\n" . 'get' . "\n" . 'picture?idUser=' . $argv[4] . "\n" . md5(null) . "\n";
	$url .= '?idUser=' . $argv[4];
}elseif($argv[1] == 'get'){
	$toHash = $time . "\n" . 'get' . "\n" . 'picture/get' . "\n" . md5(null) . "\n";
	$url .= '/get';
}

$hmac = $argv[2] . ':' . sha1_hmac($argv[3], $toHash);
$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get($url);
if($argv[1] == 'show'){
	echo $curl->response;
}else{
	var_dump($curl->response);
}
?>
