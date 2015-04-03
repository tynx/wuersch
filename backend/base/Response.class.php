<?php

/**
 * This class represents an response. It currently only supports json!
 * @author Tim Luginbühl (tynx)
 */
class Response {

	/**
	 * All the different response-objects are stored in here
	 */
	private $responses = array();

	/**
	 * The overall status of the request/response. should be OK or FAIL.
	 */
	private $status = null;

	/**
	 * A detailed message of the status (especially needed for errors)
	 */
	private $statusMessage = null;

	/**
	 * The content-type of the response.
	 */
	private $contentType = null;

	/**
	 * The constructor sets default values
	 */
	public function __construct() {
		$this->status = 'OK';
		$this->statusMessage = 'All good.';
		$this->contentType = 'application/json';
	}

	/**
	 * Returns the content-type of the response
	 * @return the content-type of the response
	 */
	public function getContentType() { 
		return $this->contentType;
	}

	/**
	 * Returns the body of the response as a string, and as we use
	 * JSON in the backend it will automatically encoded.
	 * @return string of the response
	 */
	public function getBody() {
		$arr = array(
			'status'        => $this->status,
			'statusMessage' => $this->statusMessage,
			'responses'     => $this->responses,
		);
		return json_encode($arr);
	}

	/**
	 * Adds a part-response (single object) to response
	 * @param the object/array to add
	 */
	public function addResponse($response) {
		$this->responses[] = $response;
	}

	/**
	 * This will mark the whole response as a failure and set the
	 * according error-message which can be provided.
	 * @param errorMessage the errorMessage sent to the client.
	 */
	public function markAsError($errorMessage = 'An error occured!') {
		$this->status = 'FAIL';
		$this->statusMessage = $errorMessage;
	}
}
