<?php

abstract class BaseController {

	protected $postData = array();
	protected $request = null;
	private $response = null;
	protected $user = null;
	private $store = null;

	public final function __construct() {
		$this->response = new Response();
		$this->store = new Store();
	}

	public abstract function actionRequiresAuth($name);

	public final function setRequest($request) {
		if ($request->getHeaderField('Content-Type') === 'application/json') {
			$this->postData = json_decode($request->getPostData(), true);
		}
		$this->request = $request;
	}

	public final function setUser($user) {
		$this->user = $user;
	}

	public final function getResponse() {
		return $this->response;
	}

	protected final function getStore() {
		return $this->store;
	}

	protected final function error($errorMessage) {
		$this->response->markAsError($errorMessage);
	}

	protected final function addResponse($type, $data, $additionalStatus = null) {
		$response = array(
			'type' => $type,
			'data' => $data,
		);
		if ($additionalStatus !== null) {
			$response['additionalStatus'] = $additionalStatus;
		}
		$this->response->addResponse($response);
	}
}
