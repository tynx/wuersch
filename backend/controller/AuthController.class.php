<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

class AuthController extends BaseController{

	private $facebook = null;
	
	public function __construct(){
		FacebookSession::setDefaultApplication(
			Config::$FACEBOOK_APP_ID,
			Config::$FACEBOOK_APP_SECRET
		);
	}

	public function actionRequiresAuth($name){
		if($name == 'actionFetch')
			return true;
		return false;
	}

	public function actionRegister($secret){
		$store = new Store();
		$id = $store->insert(
			'user',
			array(
				'register_time'=>time(),
				'secret'=>$secret,
			)
		);
		$store->update('user', $id, array('id_md5'=>md5($id)));
		$response = new JSONResponse();
		$response->put('id', md5($id));
		$response->put('webviewUrl', 'http://localhost/wuersch/backend/auth/authenticate?id=' . md5($id));
		$this->setResponse($response);
	}

	public function actionAuthenticate($id){
		$store = new Store();
		$user = $store->getById('user', $id);
		if($user === null)
			die('user not found!');
		if($user->authenticated_time > 0)
			die('already authenticated!');
		$_SESSION['wuersch_registration_user_id'] = $id;
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/backend/auth/callback');
		$loginUrl = $helper->getLoginUrl(array('scope' => 'user_photos,user_status,public_profile,publish_stream'));
		header('Location: ' . $loginUrl);
		exit(0);
	}

	public function actionCallback(){
		$id = $_SESSION['wuersch_registration_user_id'];
		$store = new Store();
		$user = $store->getById('user', $id);
		if($user === null)
			die('user not found!');
		if($user->authenticated_time > 0)
			die('already authenticated!');
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/backend/auth/callback');
		try {
			$session = $helper->getSessionFromRedirect();
		} catch(FacebookRequestException $ex) {
		} catch(\Exception $ex) {}
		if ($session) {
			try {
				$u = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
				$isMale = ((strtolower($u->getGender()) == 'male') ? true : false);
				$isFemale = ((strtolower($u->getGender()) == 'female') ? true : false);
				$columns = array(
					'name' => $u->getName(),
					'fb_id' =>  $u->getId(),
					'fb_access_token' => (string)$session->getAccessToken(),
					'is_male' => $isMale,
					'is_female' => $isFemale,
					'interested_in_male'=>$isFemale,
					'interested_in_female'=>$isMale,
					'authenticated_time' => time(),
				);
				$store->update('user', $id, $columns);
				exit(0);
			} catch(FacebookRequestException $e) {
			}  
		}
	}


	public function actionFetch(){
		//if($this->user->setup_time > 0)
			//die('already setup\'d!');
		$store = new Store();
		$session = new FacebookSession($this->user->fb_access_token);
		try{
			$graph = (new FacebookRequest($session, 'GET', '/' . $this->user->fb_id . '/photos/uploaded'))->execute()->getGraphObject(GraphUser::className());
		}catch(Exception $ex){
			var_dump($ex);
		}
		$curl = new Curl();
		$curl->setOpt(CURLOPT_ENCODING , 'gzip');
		foreach($graph->getProperty('data')->asArray() as $i => $pic){
			$img = array(
				'id_user' => $this->user->id,
				'fb_id' => $pic->id,
				'default' => false,
			);
			if($i == 0)
				$img['default'] = true;
			$id = $store->insert('picture', $img);
			$store->update('picture', $id, array('id_md5'=>md5($id)));
			$curl->download($pic->source, WEBROOT . Config::$USER_PICTURES . md5($id) . '.jpg');
		}
		$columns = array(
			'setup_time' => time(),
		);
		$store->update('user', $this->user->id_md5, $columns);
	}
}

?>
