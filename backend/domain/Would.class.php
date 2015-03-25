<?php

class Would{
	public function getPublicData(){
		$store = new Store();
		$userWould = $store->getById('user', $this->id_user_would);
		$user = $store->getById('user', $this->id_user);
		return array(
			'idUserWould' => $userWould->id_md5,
			'idUser'      => $user->id_md5,
			'would'       => $this->would,
			'time'        => $this->time,
		);
	}
}

?>
