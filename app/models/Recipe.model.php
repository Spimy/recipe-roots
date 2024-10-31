<?php

class Recipe extends Model {
	protected $userId;

	public function __construct() {
		$this->userId = $this->foreignKey( new User, true );
		parent::__construct();
	}
}