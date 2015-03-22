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
		if($name == 'actionSetup')
			return true;
		return false;
	}

	public function actionRegister(){
		if(!isset($this->postData['secret']) || empty($this->postData['secret']))
			return;
		$store = new Store();
		$id = $store->insert(
			'user',
			array(
				'register_time'=>time(),
				'secret'=>$this->postData['secret']
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
		$userArr = $store->getById('user', $id);
		if($userArr === null)
			die('user not found!');
		$user = new User($userArr);
		if($user->authenticatedTime > 0)
			die('already authenticated!');
		$_SESSION['wuersch_registration_user_id'] = $id;
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/backend/auth/callback');
		$loginUrl = $helper->getLoginUrl(array('req_perms' => 'user_photos'));
		header('Location: ' . $loginUrl);
		exit(0);
	}

	public function actionCallback(){
		$id = $_SESSION['wuersch_registration_user_id'];
		$store = new Store();
		$userArr = $store->getById('user', $id);
		if($userArr === null)
			die('user not found!');
		$user = new User($userArr);
		
		
		
		if($user->authenticatedTime > 0)
			die('already authenticated!');
			
		echo "all good gooooo";
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/backend/auth/callback');
		try {
			$session = $helper->getSessionFromRedirect();
		} catch(FacebookRequestException $ex) {
		} catch(\Exception $ex) {}
		if ($session) {
			try {
				echo "hehe";
				$u = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
				$columns = array(
					'name' => $u->getName(),
					'fb_id' =>  $u->getId(),
					'fb_access_token' => (string)$session->getAccessToken(),
					'is_male' => ((strtolower($u->getGender()) == 'male') ? true : false),
					'is_female' => ((strtolower($u->getGender()) == 'female') ? true : false),
					'authenticated_time' => time(),
				);
				var_dump($columns);
				$store->update('user', $id, $columns);
				exit(0);
			} catch(FacebookRequestException $e) {
			}  
		}
	}


	public function actionSetup(){
		if($this->user->setupTime > 0)
			die('already setup\'d!');
		$store = new Store();
		$session = new FacebookSession($this->user->fbAccessToken);
		$graph = (new FacebookRequest($session, 'GET', '/me/photos/uploaded'))->execute()->getGraphObject(GraphUser::className());
		$curl = new Curl();
		$curl->setOpt(CURLOPT_ENCODING , 'gzip');
		foreach($graph->getProperty('data')->asArray() as $i => $pic){
			
			$img = array(
				'id_user' => $this->user->id,
				'fb_id' => $pic->id,
				'front' => false,
			);
			if($i == 0)
				$img['front'] = true;
			$id = $store->insert('picture', $img);
			$store->update('picture', $id, array('id_md5'=>md5($id)));
			$curl->download($pic->source, Config::$USER_PICTURES . md5($id) . '.jpg');
		}
		$columns = array(
			'setup_time' => time(),
		);
		$store->update('user', $this->user->id_md5, $columns);
	}
}

?>
