<?php

include('include.php');

if(!isset($argv[1]))
	die("Provide secret!\n");


$curl = new Curl();
$curl->get('http://localhost/wuersch/backend/auth/register?secret=' . $argv[1]);

echo "your client ID: " . $curl->response->id . "\n";
echo "open following ULR in browser/webivew: " . $curl->response->webviewUrl . "\n";


?>
