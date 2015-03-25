<?php

include('include.php');

if(!isset($argv[1]) || ($argv[1]!='would' && $argv[1]!='wouldnot' && $argv[1]!='get'))
	die("Provide action! would, wouldnot, get");
if(!isset($argv[2]))
	die("Provide ID!");
if(!isset($argv[3]))
	die("Provide secret!");
if(!isset($argv[4]) && ($argv[1]=='would' || $argv[1]=='wouldnot'))
	die("Provide other user!");

$time = time();

$url = 'http://localhost/wuersch/backend/would';
if($argv[1] == 'get'){
	$toHash = $time . "\n" . 'get' . "\n" . 'would/get' . "\n" . md5(null) . "\n";
	$url .= '/get';
}elseif($argv[1] == 'wouldnot'){
	$toHash = $time . "\n" . 'get' . "\n" . 'would/not?idUser=' . $argv[4] . "\n" . md5(null) . "\n";
	$url .= '/not?idUser=' . $argv[4];
}elseif($argv[1] == 'would'){
	$toHash = $time . "\n" . 'get' . "\n" . 'would?idUser=' . $argv[4] . "\n" . md5(null) . "\n";
	$url .= '?idUser=' . $argv[4];
}

echo $toHash;
$hmac = $argv[2] . ':' . sha1_hmac($argv[3], $toHash);
$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get($url);
var_dump($curl->response);
?>
