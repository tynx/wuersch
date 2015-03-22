<?php

include('include.php');



$id = $argv[1];
$secret = 'mysecret';
$time = time();
$toHash = $time . "\nget\nauth/setup\n\n";
$hmac = $id . ':' . sha1_hmac($secret, $toHash);


$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get('http://localhost/wuersch/backend/auth/setup');
var_dump($curl->response);

?>
