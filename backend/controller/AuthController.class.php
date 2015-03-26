<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

class AuthController extends BaseController{

	public function actionRequiresAuth($name){
		if($name == 'actionFetch')
			return true;
		return false;
	}

	public function actionRegister($secret){
		$store = new Store();
		$id = $store->insert('user', array(
			'register_time'=>time(),
			'secret'=>$secret,
		));
		$store->update('user', $id, array('id_md5'=>md5($id)));
		$this->response->addResponse(array(
			'type'=>'registration',
			'data'=>array(
				'id' => md5($id),
				'authenticationURL' => 'http://localhost/wuersch/backend/auth/authenticate?id=' . md5($id),
			),
		));
	}

	public function actionAuthenticate($id){
		$this->initFB();
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
		$this->initFB();
		$id = $_SESSION['wuersch_registration_user_id'];
		$store = new Store();
		$user = $store->getById('user', $id);
		if($user === null){
			$this->error('user was not found!');
			return;
		}
		if($user->authenticated_time > 0){
			$this->error('this user was already authenticated!');
			return;
		}
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/backend/auth/callback');
		try {
			$session = $helper->getSessionFromRedirect();
		} catch(FacebookRequestException $ex) {
			$this->error($e->toString());
			return;
		} catch(\Exception $ex) {
			$this->error($e->toString());
			return;
		}
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
			} catch(FacebookRequestException $e) {
				$this->error($e->toString());
				return;
			}  
		}
	}


	public function actionFetch(){
		$this->initFB();
		$store = new Store();
		$session = new FacebookSession($this->user->fb_access_token);
		try{
			$request = new FacebookRequest($session, 'GET', '/' . $this->user->fb_id . '/photos/uploaded');
			$graph = $request->execute()->getGraphObject(GraphUser::className());
		}catch(Exception $ex){
			$this->error($ex->toString());
			return;
		}
		$curl = new Curl();
		$curl->setOpt(CURLOPT_ENCODING , 'gzip');
		if(!is_array($graph->getProperty('data')->asArray())){
			$this->error('We did not receive valid data for fetching images.');
			return;
		}
		foreach($graph->getProperty('data')->asArray() as $i => $pic){
			$found = $store->getByColumns('picture', array('fb_id'=>$pic->id, 'id_user'=>$this->user->id));
			if(count($found) > 0)
				continue;
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
			$picture = $store->getById('picture', $id);
			$this->response->addResponse(array(
				'type'=>'picture',
				'additional_status'=>'downloaded.',
				'data'=>$picture->getPublicData(),
			));
		}
		$columns = array(
			'setup_time' => time(),
		);
		$store->update('user', $this->user->id_md5, $columns);
	}

	private function initFB(){
		FacebookSession::setDefaultApplication(
			Config::$FACEBOOK_APP_ID,
			Config::$FACEBOOK_APP_SECRET
		);
	}
}

?>
