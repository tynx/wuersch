<?php

include('include.php');

$id = $argv[1];
$secret = 'mysecret';
$time = time();
$toHash = $time . "\nget\nuser/current\n" . md5(null) . "\n";

$hmac = $id . ':' . sha1_hmac($secret, $toHash);

$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get('http://localhost/wuersch/backend/user/current');
var_dump($curl->response);


?>