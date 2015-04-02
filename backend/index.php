<?php

session_start();

define('WEBROOT', __DIR__);
include('include.php');



$request = new Request();
$backend = new Backend($request);
$backend->run();
$response = $backend->getResponse();
header('Content-Type: ' . $response->getContentType());
echo $response->getBody();
exit(0);
