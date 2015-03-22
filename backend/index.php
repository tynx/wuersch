<?php

session_start();

include('include.php');

$baseLocation = '/wuersch/backend/';

$request = new Request();
if(!$request->isValid())
	die("Invalid Request!");
$controllerName = $request->getControllerName();
$actionName = $request->getActionName();
if(!class_exists($controllerName, false))
	die("Not found!");

$controller = new $controllerName();
if (!is_subclass_of($controller, 'BaseController'))
	die("Invalid controller!");

if (!method_exists($controller, $actionName))
	die("Invalid action");

if($controller->actionRequiresAuth($actionName) && !$request->isAuthenticated()){
	die("Auth required!");
}

if($request->getAuthenticatedUser() !== null)
	$controller->setUser($request->getAuthenticatedUser());

$controller->setRequest($request);

$arguments = array();
$rm = new ReflectionMethod($controllerName, $actionName);

$params = $rm->getParameters();
foreach ($params as $i => $param){
	if (!$param->isOptional() && $request->getArgument($param->getName()) === null) {
		die("arguments missing");
	}
	$arguments[] = $request->getArgument($param->getName());
}

call_user_func_array(array($controller, $actionName), $arguments);

$response = $controller->getResponse();

header('Content-Type: ' . $response->getContentType());
echo $response->getBody();

?>
