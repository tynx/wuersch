<?php

abstract class BaseController{

	protected $postData = array();
	protected $request = null;
	protected $response = null;
	protected $user = null;

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

	protected final function setResponse($response){
		$this->response = $response;
	}

	public final function getResponse(){
		if($this->response === null)
			return new Response();
		return $this->response;
	}
}

?>
