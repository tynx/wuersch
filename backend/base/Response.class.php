<?php

class Response {
	private $responses = array();
	private $status = null;
	private $statusMessage = null;
	private $contentType = null;

	public function __construct($contentType = 'application/json') {
		$this->status = 'OK';
		$this->statusMessage = 'All good.';
		$this->contentType = $contentType;
	}

	public function getContentType() { 
		return $this->contentType;
	}

	public function getBody() {
		$arr = array(
			'status'        => $this->status,
			'statusMessage' => $this->statusMessage,
			'responses'     => $this->responses,
		);
		return json_encode($arr);
	}

	public function addResponse($response) {
		$this->responses[] = $response;
	}

	public function markAsError($errorMessage = 'An error occured!') {
		$this->status = 'FAIL';
		$this->statusMessage = $errorMessage;
	}
}
