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
		return false;
	}

	public function actionRegister(){
		if(!isset($this->postData['secret']) || empty($this->postData['secret']))
			return;
		$store = new Store();
		$id = $store->insert(
			'user',
			array(
				'last_seen'=>time(),
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
		// check if exists
		$_SESSION['wuersch_registration_user_id'] = $id;
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/backend/auth/callback');
		$loginUrl = $helper->getLoginUrl();
		header('Location: ' . $loginUrl);
		exit(0);
	}

	public function actionCallback(){
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/backend/auth/callback');
		try {
			$session = $helper->getSessionFromRedirect();
		} catch(FacebookRequestException $ex) {
		} catch(\Exception $ex) {}
		if ($session) {
			try {
				$u = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
				$store = new Store();
				$columns = array(
					'name' => $u->getName(),
					'fb_id' =>  $u->getId(),
					'isMale' => ((strtolower($u->getGender()) == 'male') ? true : false),
					'isFemale' => ((strtolower($u->getGender()) == 'female') ? true : false),
				);
				var_dump($columns);
				$store->update('user', $_SESSION['wuersch_registration_user_id'], $columns);
				
				exit(0);
			} catch(FacebookRequestException $e) {
			}  
		}
	}
}

?>
