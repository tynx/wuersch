<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

/**
 * This class handles to whole Facebook-Integration. Three actions:
 * authenticate (which redirects to facebook)
 * callback (which should NOT be called. this is called/redirect by/from
 * facebook)
 * fetch (downloads the FB-pictures of the user)
 * @author Tim Luginbühl (tynx)
 */
class AuthController extends BaseController {

	/**
	 * Which methods need an HMAC-Authentication
	 * in this class: only fetch
	 * @param name the name of the method which needs auth
	 * @return true if the method needs a valid user
	 */
	public function actionRequiresAuth($name) {
		if ($name === 'actionFetch') {
			return true;
		}
		return false;
	}

	private function _initFB() {
		FacebookSession::setDefaultApplication(
			Config::FACEBOOK_APP_ID,
			Config::FACEBOOK_APP_SECRET
		);
	}

	/**
	 * First authentication step. Builds session-based FB-login and
	 * redirects to fb for approval of the user.
	 */
	public function actionAuthenticate($idUser) {
		$this->_initFB();
		$user = $this->getStore()->getById('user', $idUser);
		if ($user === null) {
			$this->error('User not found.');
		}
		if ($user->authenticated_time > 0) {
			$this->error('Already authenticated.');
		}
		$_SESSION['wuersch_registration_user_id'] = $idUser;
		$helper = new FacebookRedirectLoginHelper(Config::FACEBOOK_APP_REDIRECT_URL);
		$loginUrl = $helper->getLoginUrl(array('scope' => Config::FACEBOOK_APP_SCOPES));
		header('Location: ' . $loginUrl);
		exit(0);
	}

	public function actionCallback() {
		$this->_initFB();
		$id = $_SESSION['wuersch_registration_user_id'];
		$user = $this->getStore()->getById('user', $id);
		if ($user === null) {
			$this->error('user was not found!');
			return;
		}
		if ($user->authenticated_time > 0) {
			$this->error('this user was already authenticated!');
			return;
		}
		$helper = new FacebookRedirectLoginHelper(Config::FACEBOOK_APP_REDIRECT_URL);
		try {
			$session = $helper->getSessionFromRedirect();
		} catch (FacebookRequestException $ex) {
			$this->error($e->toString());
			return;
		} catch (\Exception $ex) {
			$this->error($e->toString());
			return;
		}
		if ($session) {
			try {
				$request = new FacebookRequest($session, 'GET', '/me');
				$fbUser = $request->execute()->getGraphObject(GraphUser::className());
				$isMale = ((strtolower($fbUser->getGender()) === 'male') ? true : false);
				$isFemale = ((strtolower($fbUser->getGender()) === 'female') ? true : false);
				$columns = array(
					'name'                 => $fbUser->getName(),
					'id_fb'                => $fbUser->getId(),
					'fb_access_token'      => $session->getAccessToken(),
					'is_male'              => $isMale,
					'is_female'            => $isFemale,
					'interested_in_male'   => $isFemale,
					'interested_in_female' => $isMale,
					'authenticated_time'   => time(),
				);
				$this->getStore()->updateById('user', $id, $columns);
				return;
			} catch (FacebookRequestException $e) {
				$this->error($e);
				return;
			}  
		}
		$this->error('Was this URL called by Facebook or yourself?!');
	}


	public function actionFetch() {
		$this->_initFB();
		$session = new FacebookSession($this->user->fb_access_token);
		try {
			$request = new FacebookRequest($session, 'GET', '/me/photos/uploaded');
			$graph = $request->execute()->getGraphObject();
		} catch (Exception $ex) {
			$this->error('Couldn\'t access Facebook Graph API.');
			return;
		}
		$curl = new Curl();
		$curl->setOpt(CURLOPT_ENCODING, 'gzip');
		if (!is_array($graph->getProperty('data')->asArray())) {
			$this->error('We did not receive valid data for fetching images.');
			return;
		}
		foreach ($graph->getProperty('data')->asArray() as $i => $pic) {
			$found = $this->getStore()->getByColumns(
				'picture',
				array('id_fb' => $pic->id, 'id_user' => $this->user->id)
			);
			if (count($found) > 0) {
				continue;
			}
			$img = array(
				'id_user' => $this->user->id,
				'id_fb'   => $pic->id,
				'default' => false,
				'time'    => time(),
			);
			if ($i === 0) {
				$img['default'] = true;
			}
			$id = $this->getStore()->insert('picture', $img);
			$this->getStore()->updateById('picture', $id, array('id_md5' => md5($id)));
			$curl->download($pic->source, WEBROOT . Config::USER_PICTURES . md5($id) . '.jpg');
			$picture = $this->getStore()->getById('picture', $id);
			$this->addResponse('picture', $picture->getPublicData(), 'downloaded');
		}
		$columns = array(
			'fetch_time' => time(),
		);
		$this->getStore()->updateById('user', $this->user->id_md5, $columns);
	}
}
