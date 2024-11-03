<?php

const DIETARY_TYPES = [ 'vegetarian', 'vegan', 'halal' ];

class User extends Model {
	protected $email;
	protected $password;
	protected $dietaryType;
	protected $verified;

	public function __construct() {
		$this->email = $this->charField( 255, false, true );
		$this->password = $this->charField( 255, false );
		$this->dietaryType = $this->charField( 10, true, false );
		$this->verified = $this->booleanField( false );
		parent::__construct();
	}
}