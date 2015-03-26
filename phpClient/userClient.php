<?php

include('include.php');

if(!isset($argv[1]) || ($argv[1]!='current' && $argv[1]!='random' && $argv[1]!='settings'))
	die("Provide action! current, random, settings");
if(!isset($argv[2]))
	die("Provide ID!");
if(!isset($argv[3]))
	die("Provide secret!");
if(!isset($argv[4]) && ($argv[1]=='settings'))
	die("Provide File!");

if($argv[1]=='settings')
	die("implement!");

$time = time();

$url = 'http://localhost/wuersch/backend/user/';
if($argv[1] == 'current'){
	$toHash = $time . "\n" . 'get' . "\n" . 'user/current' . "\n" . md5(null) . "\n";
	$url .= 'current';
}elseif($argv[1] == 'random'){
	$toHash = $time . "\n" . 'get' . "\n" . 'user/random' . "\n" . md5(null) . "\n";
	$url .= 'random';
}elseif($argv[1] == 'settings'){
	$toHash = $time . "\n" . 'post' . "\n" . 'user/settings' . "\n" . md5(null) . "\n";
	$url .= 'settings';
}
echo $toHash;
$hmac = $argv[2] . ':' . sha1_hmac($argv[3], $toHash);
$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get($url);
var_dump($curl->response);
?>
