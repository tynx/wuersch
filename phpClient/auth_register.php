<?php

include('include.php');

if(!isset($argv[1]))
	die("Provide secret!\n");


$curl = new Curl();
$curl->get('http://localhost/wuersch/backend/auth/register?secret=' . $argv[1]);

if($curl->response->status !== 'OK')
	die("Something wrong with backend?!");

echo "your client ID: " . $curl->response->responses[0]->id . "\n";
echo "open following ULR in browser/webivew: " . $curl->response->responses[0]->authenticationURL . "\n";


?>
