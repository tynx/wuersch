<?php

include('include.php');

$id = $argv[1];
$secret = $argv[2];
$time = time();
$toHash = $time . "\nget\nuser/random\n" . md5(null) . "\n";

$hmac = $id . ':' . sha1_hmac($secret, $toHash);

$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get('http://localhost/wuersch/backend/user/random');
$randomUser = $curl->response->randomUser;

//var_dump($curl->response);
$time = time();
$toHash = $time . "\n" . 'get' . "\n" . 'picture?idUser=' . $randomUser->id . "\n" . md5(null) . "\n";
$hmac = $id . ':' . sha1_hmac($secret, $toHash);

$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get('http://localhost/wuersch/backend/picture?idUser=' . $randomUser->id);
echo $curl->response;

?>
