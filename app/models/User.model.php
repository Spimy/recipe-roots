<?php

class User extends Model {
	protected $username;

	public function __construct() {
		$this->username = $this->charField( 16, false, true );
		parent::__construct( 'userId' );
	}

	static function test() {

	}
}