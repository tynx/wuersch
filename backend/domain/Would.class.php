<?php

/**
 * This class represents a would from the mysql-table would.
 * @author Tim Luginbühl (tynx)
 */
class Would {

	/**
	 * This method returns the public data of a would.
	 * @return array of the all the data which can be made public.
	 */
	public function getPublicData() {
		$store = new Store();
		$userWould = $store->getById('user', $this->id_user_would);
		$user = $store->getById('user', $this->id_user);
		return array(
			'idUserWould' => $userWould->id_md5,
			'idUser'      => $user->id_md5,
			'would'       => (boolean)$this->would,
			'time'        => (int)$this->time,
		);
	}
}
