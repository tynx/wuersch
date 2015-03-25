<?php

include('include.php');

if(!isset($argv[1]))
	die("Provide ID!");
if(!isset($argv[2]))
	die("Provide secret!");
if(!isset($argv[3]))
	die("Provide other user");


$id = $argv[1];
$secret = $argv[2];
$idUser = $argv[3];

$time = time();
$toHash = $time . "\n" . 'get' . "\n" . 'would?idUser=' . $idUser . "\n" . md5(null) . "\n";
$hmac = $id . ':' . sha1_hmac($secret, $toHash);

$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get('http://localhost/wuersch/backend/would?idUser=' . $idUser);
echo $curl->response;
?>
