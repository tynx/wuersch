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

	public function actionRandom(){
		$query = 'SELECT `user`.`id` FROM `wuersch`.`user` WHERE NOT EXISTS';
		$query .= '(SELECT `would`.`id_user_would` FROM `wuersch`.`would` ';
		$query .= 'WHERE `user`.`id`=`would`.`id_user`) AND `user`.`id`';
		$query .= '!=' . $this->user->id . ' AND ';
		$query .= '`user`.`is_male`=' . $this->user->interested_in_male . ' AND ';
		$query .= '`user`.`is_female`=' . $this->user->interested_in_female . ' ';
		$query .= 'LIMIT 100;';
		$store = new Store();
		$result = $store->getByCustomQuery($query);
		if(count($result)==0)
			die("no user found!");
		$user = $store->getById('user', $result[rand(0, count($result)-1)]['id']);
		$columns = array(
			'id_user'=>$user->id,
			'default'=>1,
		);
		$picture = $store->getByColumns('picture', $columns);
		$randomUser = array(
			'id'=>$user->id_md5,
			'name'=>$user->name,
			'last_seen'=>$user->last_seen,
			'picture'=>$picture[0]->id_md5,
		);
		
		$response = new JSONResponse();
		$response->put('randomUser', $randomUser);
		$this->response = $response;
	}
}

?>
