<?php

include('include.php');

$json = json_encode( array('secret' => 'mysecret'));
$curl = new Curl();
$curl->setHeader('Content-Type', 'application/json');
$curl->post('http://localhost/wuersch/backend/auth/register',$json);
var_dump($curl->response);


?>
