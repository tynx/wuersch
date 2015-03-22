<?php

class User{
	public $id = null;
	public $id_md5 = null;
	public $fbAccessToken = null;
	public $registerTime = 0;
	public $authenticatedTime = 0;
	public $setupTime = 0;

	public function __construct($attributes = null){
		if(is_array($attributes) && !empty($attributes))
			$this->populate($attributes);
	}

	private function populate($assocArray){
		if(isset($assocArray['id']) && !empty($assocArray['id']))
			$this->id = $assocArray['id'];
		if(isset($assocArray['id_md5']) && !empty($assocArray['id_md5']))
			$this->id_md5 = $assocArray['id_md5'];
		if(isset($assocArray['fb_access_token']) && !empty($assocArray['fb_access_token']))
			$this->fbAccessToken = $assocArray['fb_access_token'];
		if(isset($assocArray['register_time']) && !empty($assocArray['register_time']))
			$this->registerTime = $assocArray['register_time'];
		if(isset($assocArray['authenticated_time']) && !empty($assocArray['authenticated_time']))
			$this->authenticatedTime = $assocArray['authenticated_time'];
		if(isset($assocArray['setup_time']) && !empty($assocArray['setup_time']))
			$this->setupTime = $assocArray['setup_time'];
	}
}

?>
