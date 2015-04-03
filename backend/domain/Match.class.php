<?php

/**
 * This class represents a match from the mysql-table match.
 * @author Tim Luginbühl (tynx)
 */
class Match {

	/**
	 * This method returns the public data of a match.
	 * @return array of the all the data which can be made public.
	 */
	public function getPublicData() {
		$store = new Store();
		$user1 = $store->getById('user', $this->id_user_1);
		$user2 = $store->getById('user', $this->id_user_2);
		return array(
			'idUser1' => $user1->id_md5,
			'idUser2' => $user2->id_md5,
			'time'    => (int)$this->time,
		);
	}
}

