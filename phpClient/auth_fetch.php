<?php

include('include.php');

if(!isset($argv[1]))
	die("Provide ID!");
if(!isset($argv[2]))
	die("Provide secret!");

$id = $argv[1];
$secret = $argv[2];
$time = time();
$toHash = $time . "\nget\nauth/fetch\n" . md5(null) . "\n";
$hmac = $id . ':' . sha1_hmac($secret, $toHash);


$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get('http://localhost/wuersch/backend/auth/fetch');
var_dump($curl->response);

?>
