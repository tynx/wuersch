<?php

include('include.php');

$data = array('interestedInMale' => false, 'interestedInFemale'=> true);

$json = json_encode($data);

$id = $argv[1];
$secret = 'mysecret';
$time = time();
$toHash = $time . "\npost\nuser/settings\n" . md5($json) . "\n";
echo '>' . $toHash . '<';
$hmac = $id . ':' . sha1_hmac($secret, $toHash);


$curl = new Curl();
$curl->setHeader('hmac', $hmac);
$curl->setHeader('timestamp', $time);
$curl->setHeader('Content-Type', 'application/json');
$curl->post('http://localhost/wuersch/backend/user/settings', $json);
var_dump($curl->response);


?>
