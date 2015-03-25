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
var_dump($curl->response);


?>
