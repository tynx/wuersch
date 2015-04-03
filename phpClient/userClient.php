<?php

include('include.php');

if(!isset($argv[1]) || ($argv[1]!='current' && $argv[1]!='random' && $argv[1]!='settings' && $argv[1] != 'register'))
	die("Provide action! current, random, settings, register");
if(!isset($argv[2]))
	die("Provide ID or secret for register!");
if(!isset($argv[3]) && $argv[1]!='register')
	die("Provide secret!");


$body = '';
if($argv[1]=='settings'){
	$body = json_encode(array(
		'interestedInMale'=>true,
		'interestedInFemale'=>true,
	));
}


$time = time();

$url = 'http://localhost/wuersch/backend/user/';
if($argv[1] == 'current'){
	$toHash = $time . "\n" . 'get' . "\n" . 'user/current' . "\n" . md5(null) . "\n";
	$url .= 'current';
}elseif($argv[1] == 'random'){
	$toHash = $time . "\n" . 'get' . "\n" . 'user/random' . "\n" . md5(null) . "\n";
	$url .= 'random';
}elseif($argv[1] == 'settings'){
	$toHash = $time . "\n" . 'post' . "\n" . 'user/settings' . "\n" . md5($body) . "\n";
	$url .= 'settings';
}elseif($argv[1] == 'register'){
	$toHash = $time . "\n" . 'get' . "\n" . 'user/register?secret=' . $argv[2] . "\n" . md5(null) . "\n";
	$url .= 'register?secret=' . $argv[2];
}

$curl = new Curl();
if($argv[1] != 'register'){
	$hmac = $argv[2] . ':' . sha1_hmac($argv[3], $toHash);
	$curl->setHeader('hmac', $hmac);
	$curl->setHeader('timestamp', $time);
}

if($argv[1]=='settings'){
	$curl->setHeader('Content-Type', 'application/json');
	$curl->post($url, $body);
}else{
	$curl->get($url);
}

var_dump($curl->response);
echo "\n";
?>
