<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

class AuthController extends BaseController{

	private $facebook = null;
	
	public function __construct(){
		FacebookSession::setDefaultApplication('1610463259187195', '99634d817b7bf44c4c56ff38823cbd7c');
	}

	public function actionIndex(){
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/auth/finish');
		$hash = sha1(time());
		$loginUrl = $helper->getLoginUrl();
		echo json_encode(array('hash'=>$hash, 'loginUrl'=>$loginUrl));
	}

	public function actionFinish(){
		$helper = new FacebookRedirectLoginHelper('http://localhost/wuersch/auth/finish');
		try {
			$session = $helper->getSessionFromRedirect();
		} catch(FacebookRequestException $ex) {
		} catch(\Exception $ex) {}
		if ($session) {
			try {
				$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
				echo "Name: " . $user_profile->getName();
			} catch(FacebookRequestException $e) {
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
			}  
		}else{
			echo "No session!!!";
		}
	}
}

?>
