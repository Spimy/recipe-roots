<?php

class Recipe extends Model {
	protected $userId;
	protected $title;
	protected $thumbnail;
	protected $prepTime;
	protected $waitingTime;
	protected $servings;
	protected $public;
	protected $ingredients;
	protected $instructions;

	public function __construct() {
		$this->userId = $this->foreignKey( new User, true );
		$this->title = $this->charField( 100 );
		$this->thumbnail = $this->charField( 255, true );
		$this->prepTime = $this->integerField( true );
		$this->waitingTime = $this->integerField( true );
		$this->servings = $this->integerField( true );
		$this->public = $this->booleanField( false );
		$this->ingredients = $this->jsonField();
		$this->instructions = $this->textField( false );
		parent::__construct();
	}
}