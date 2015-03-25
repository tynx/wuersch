<?php

abstract class BaseController{

	protected $postData = array();
	protected $request = null;
	protected $response = null;
	protected $user = null;

	public final function __construct(){
		$this->response = new Response();
	}

	public abstract function actionRequiresAuth($name);

	public final function setRequest($request){
		if($request->getHeaderField('Content-Type') === 'application/json'){
			$this->postData = json_decode($request->getPostData(), true);
		}
		$this->request = $request;
	}

	public final function setUser($user){
		$this->user = $user;
	}

	public final function getResponse(){
		return $this->response;
	}

	protected final function error($errorMessage){
		$this->response->markAsError('user was not found!');
	}
}

?>
