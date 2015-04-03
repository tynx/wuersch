<?php

/**
 * This class represents a user from the mysql-table user.
 * @author Tim Luginbühl (tynx)
 */
class User {

	/**
	 * This method returns the public data of a user.
	 * @return array of the all the data which can be made public.
	 */
	public function getPublicData() {
		return array(
			'id'                 => $this->id_md5,
			'name'               => $this->name,
			'lastSeen'           => (int)$this->last_seen,
			'isMale'             => (boolean)$this->is_male,
			'isFemale'           => (boolean)$this->is_female,
			'interestedInMale'   => (boolean)$this->interested_in_male,
			'interestedInFemale' => (boolean)$this->interested_in_female,
		);
	}
}
