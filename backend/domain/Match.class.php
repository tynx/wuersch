<?php

class Match{

	public function getPublicData(){
		$store = new Store();
		$user1 = $store->getById('user', $this->id_user_1);
		$user2 = $store->getById('user', $this->id_user_2);
		return array(
			'idUser1' => $user1->id_md5,
			'idUser2' => $user2->id_md5,
			'time'    => $this->time,
		);
	}
}

?>
