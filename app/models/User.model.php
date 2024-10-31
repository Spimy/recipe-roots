<?php

class User extends Model {
	protected $username;

	public function __construct() {
		$this->username = $this->charField( 16, false, true );
		$this->email = $this->charField( 255, false, false, 'test@gmail.com' );
		parent::__construct();
	}

	static function test() {

	}
}