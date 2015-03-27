<?php

class User{
	public function getPublicData(){
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

?>
