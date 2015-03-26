<?php

include('include.php');

if(!isset($argv[1]) || ($argv[1]!='is' && $argv[1]!='get'))
	die("Provide action! is get");
if(!isset($argv[2]))
	die("Provide ID!");
if(!isset($argv[3]))
	die("Provide secret!");
if(!isset($argv[4]) && ($argv[1]=='is'))
	die("Provide other user ID!");

$time = time();

$url = 'http://localhost/wuersch/backend/';
if($argv[1] == 'is'){
	$toHash = $time . "\n" . 'get' . "\n" . 'match/is?idUser=' . $argv[4] . "\n" . md5(null) . "\n";
	$url .= 'match/is?idUser=' . $argv[4];
}elseif($argv[1] == 'get'){
	$toHash = $time . "\n" . 'get' . "\n" . 'match/get' . "\n" . md5(null) . "\n";
	$url .= 'match/get';
}

$hmac = $argv[2] . ':' . sha1_hmac($argv[3], $toHash);
$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get($url);
var_dump($curl->response);

?>
