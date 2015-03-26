<?php

include('include.php');

if(!isset($argv[1]) || ($argv[1]!='register' && $argv[1]!='fetch'))
	die("Provide action! register, fetch");
if(!isset($argv[2]) && $argv[1]=='fetch')
	die("Provide ID!");
if(!isset($argv[3]) && $argv[1]=='fetch')
	die("Provide secret!");
if(!isset($argv[2]) && $argv[1]=='register')
	die("Provide secret!");

$time = time();

$url = 'http://localhost/wuersch/backend/auth/';
if($argv[1] == 'register'){
	$url .= 'register?secret=' . $argv[2];
}elseif($argv[1] == 'fetch'){
	$toHash = $time . "\n" . 'get' . "\n" . 'auth/fetch' . "\n" . md5(null) . "\n";
	$url .= 'fetch';
	
}


$curl = new Curl();
if($argv[1]=='fetch'){
	$hmac = $argv[2] . ':' . sha1_hmac($argv[3], $toHash);
	$curl->setHeader('hmac', $hmac);
	$curl->setHeader('timestamp', $time);
}

$curl->get($url);
var_dump($curl->response);

?>
