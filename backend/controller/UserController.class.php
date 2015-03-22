<?php

class UserController extends BaseController{

	public function actionRequiresAuth($name){
		return true;
	}

	public function actionCurrent(){
		$store = new Store();
		$response = new JSONResponse();
		//foreach($user as $key => $value)
		//	$response->put($key, $value);
		$response->put('user', $this->user);
		$pictures = $store->getByColumns('picture', array('id_user'=>$this->user->id));
		foreach($pictures as $picture){
			//$picture[]
		}
		$response->put('pictures', $pictures);
		$this->response = $response;
	}

	public function actionSettings(){
		$store = new Store();
		$columns = array();
		if(isset($this->postData['interestedInMale']))
			$columns['interested_in_male'] = $this->postData['interestedInMale'];
		if(isset($this->postData['interestedInFemale']))
			$columns['interested_in_female'] = $this->postData['interestedInFemale'];
		
		if(count($columns) > 0)
			$store->update('user', $this->user->id, $columns);
		//PIC!
	}
}

?>
