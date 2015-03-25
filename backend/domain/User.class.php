<?php

class User{
	public function getPublicData(){
		return array(
			'id'                 => $this->id_md5,
			'name'               => $this->name,
			'lastSeen'           => $this->last_seen,
			'isMale'             => $this->is_male,
			'isFemale'           => $this->is_female,
			'interestedInMale'   => $this->interested_in_male,
			'interestedInFemale' => $this->interested_in_female,
		);
	}
}

?>
