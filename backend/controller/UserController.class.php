<?php

class UserController extends BaseController{

	public function actionRequiresAuth($name){
		if($name === 'actionRegister')
			return false;
		return true;
	}

	public function actionCurrent(){
		$store = new Store();

		$this->response->addResponse(array(
			'type'=>'user',
			'data'=>$this->user->getPublicData()
		));

		$pictures = $store->getByColumns('picture', array('id_user'=>$this->user->id));
		foreach($pictures as $picture){
			$this->response->addResponse(array(
				'type'=>'picture',
				'data'=>$picture->getPublicData()
			));
		}

		$woulds = $store->getByColumns('would', array('id_user_would'=>$this->user->id));
		foreach($woulds as $would){
			$this->response->addResponse(array(
				'type'=>'would',
				'data'=>$would->getPublicData()
			));
		}
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

	public function actionSettings(){
		$store = new Store();
		$columns = array();
		if(isset($this->postData['interestedInMale']))
			$columns['interested_in_male'] = $this->postData['interestedInMale'];
		if(isset($this->postData['interestedInFemale']))
			$columns['interested_in_female'] = $this->postData['interestedInFemale'];
		
		if(count($columns) > 0)
			$store->update('user', $this->user->id, $columns);
		
		$this->response->addResponse(array(
			'type'=>'user',
			'data'=>$store->getById('user', $this->user->id)
		));
	}

	public function actionRandom(){
		$query = 'SELECT `user`.`id` FROM `wuersch`.`user` WHERE NOT EXISTS';
		$query .= '(SELECT `would`.`id_user_would` FROM `wuersch`.`would` ';
		$query .= 'WHERE `user`.`id`=`would`.`id_user`) AND `user`.`id`';
		$query .= '!=' . $this->user->id . ' AND ';
		$query .= '`user`.`is_male`=' . $this->user->interested_in_male . ' AND ';
		$query .= '`user`.`is_female`=' . $this->user->interested_in_female;
		$query .= ' AND `user`.`fetch_time` > 0 LIMIT 100;';
		$store = new Store();
		$result = $store->getByCustomQuery($query);
		if(count($result)==0){
			$this->error("No random user found!");
			return;
		}
		$user = $store->getById('user', $result[rand(0, count($result)-1)]['id']);
		$this->response->addResponse(array('type'=>'user', 'data'=>$user->getPublicData()));
		$columns = array(
			'id_user'=>$user->id,
			'default'=>1,
		);
		$picture = $store->getByColumns('picture', $columns);
		if(!is_array($picture) && count($picture) != 1){
			$this->error("No picture found for user!");
			return;
		}
		$this->response->addResponse(array('type'=>'picture', 'data'=>$picture[0]->getPublicData()));
	}
}

?>
