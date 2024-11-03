<?php

const PROFILE_TYPES = [ 
	'user' => false,
	'farmer' => true
];

class Profile extends Model {
	protected $userId;
	protected $username;
	protected $avatar;
	protected $type;


	public function __construct() {
		$this->userId = $this->foreignKey( new User(), true );
		$this->username = $this->charField( 16, false, true );
		$this->avatar = $this->charField( 255, true, false );
		$this->type = $this->booleanField( PROFILE_TYPES['user'] );
		parent::__construct();
	}
}