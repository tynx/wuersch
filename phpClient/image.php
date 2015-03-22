<?php

include('include.php');



$id = 'c4ca4238a0b923820dcc509a6f75849b';
$secret = 'mysecret';
$time = time();
$toHash = $time . "\nget\npicture?id=aab3238922bcc25a6f606eb525ffdc56\n" . md5(null) . "\n";
$hmac = $id . ':' . sha1_hmac($secret, $toHash);


$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);

$curl->get('http://localhost/wuersch/backend/picture?id=aab3238922bcc25a6f606eb525ffdc56');
//var_dump($curl->response);
echo $curl->response;
?>
