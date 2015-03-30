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

	public function actionAuthenticate($idUser){
		$this->initFB();
		$user = $this->getStore()->getById('user', $idUser);
		if($user === null)
			die('user not found!');
		if($user->authenticated_time > 0)
			die('already authenticated!');
		$_SESSION['wuersch_registration_user_id'] = $idUser;
		$helper = new FacebookRedirectLoginHelper(Config::$FACEBOOK_APP_REDIRECT_URL);
		$loginUrl = $helper->getLoginUrl(array('scope' => Config::$FACEBOOK_APP_SCOPES));
		header('Location: ' . $loginUrl);
		exit(0);
	}

	public function actionCallback(){
		$this->initFB();
		$id = $_SESSION['wuersch_registration_user_id'];
		$user = $this->getStore()->getById('user', $id);
		if($user === null){
			$this->error('user was not found!');
			return;
		}
		if($user->authenticated_time > 0){
			$this->error('this user was already authenticated!');
			return;
		}
		$helper = new FacebookRedirectLoginHelper(Config::$FACEBOOK_APP_REDIRECT_URL);
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
					'name'                 => $u->getName(),
					'id_fb'                => $u->getId(),
					'fb_access_token'      => $session->getAccessToken(),
					'is_male'              => $isMale,
					'is_female'            => $isFemale,
					'interested_in_male'   => $isFemale,
					'interested_in_female' => $isMale,
					'authenticated_time'   => time(),
				);
				$this->getStore()->update('user', $id, $columns);
				return;
			} catch(FacebookRequestException $e) {
				$this->error($e->toString());
				return;
			}  
		}
		$this->error('Was this URL called by Facebook or yourself?!');
	}


	public function actionFetch(){
		$this->initFB();
		$session = new FacebookSession($this->user->fb_access_token);
		try{
			$request = new FacebookRequest($session, 'GET', '/' . $this->user->id_fb . '/photos/uploaded');
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
			$found = $this->getStore()->getByColumns('picture', array('fb_id'=>$pic->id, 'id_user'=>$this->user->id));
			if(count($found) > 0)
				continue;
			$img = array(
				'id_user' => $this->user->id,
				'id_fb'   => $pic->id,
				'default' => false,
				'time'    => time(),
			);
			if($i == 0)
				$img['default'] = true;
			$id = $this->getStore()->insert('picture', $img);
			$this->getStore()->update('picture', $id, array('id_md5'=>md5($id)));
			$curl->download($pic->source, WEBROOT . Config::$USER_PICTURES . md5($id) . '.jpg');
			$picture = $this->getStore()->getById('picture', $id);
			$this->addResponse('picture', $picture->getPublicData(), 'downloaded');
		}
		$columns = array(
			'fetch_time' => time(),
		);
		$this->getStore()->update('user', $this->user->id_md5, $columns);
	}

	private function initFB(){
		FacebookSession::setDefaultApplication(
			Config::$FACEBOOK_APP_ID,
			Config::$FACEBOOK_APP_SECRET
		);
	}
}

?>
