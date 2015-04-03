<?php

include('include.php');

if(!isset($argv[1]) || ($argv[1]!='show' && $argv[1]!='get' && $argv[1]!='default' && $argv[1]!='own'))
	die("Provide action! get, show, default, own");
if(!isset($argv[2]))
	die("Provide ID!");
if(!isset($argv[3]))
	die("Provide secret!");
if(!isset($argv[4]) && ($argv[1]=='show' || $argv[1] == 'default' || $argv[1] == 'own'))
	die("Provide other user ID or defaultIMG id or ownPictureId!");

$time = time();

$url = 'http://localhost/wuersch/backend/picture';
if($argv[1] == 'show'){
	$toHash = $time . "\n" . 'get' . "\n" . 'picture?idUser=' . $argv[4] . "\n" . md5(null) . "\n";
	$url .= '?idUser=' . $argv[4];
}elseif($argv[1] == 'get'){
	$toHash = $time . "\n" . 'get' . "\n" . 'picture/get' . "\n" . md5(null) . "\n";
	$url .= '/get';
}elseif($argv[1] == 'default'){
	$toHash = $time . "\n" . 'get' . "\n" . 'picture/default?idPicture=' . $argv[4] . "\n" . md5(null) . "\n";
	$url .= '/default?idPicture=' . $argv[4];
}elseif($argv[1] == 'own'){
	$toHash = $time . "\n" . 'get' . "\n" . 'picture/own?idPicture=' . $argv[4] . "\n" . md5(null) . "\n";
	$url .= '/own?idPicture=' . $argv[4];
}

$hmac = $argv[2] . ':' . sha1_hmac($argv[3], $toHash);
$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get($url);
if($argv[1] == 'show' || $argv[1] == 'own'){
	echo $curl->response;
}else{
	var_dump($curl->response);
}
?>
