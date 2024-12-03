<?php

class Ingredient extends Model {
	protected $farmerId;
	protected $ingredient;
	protected $price;
	protected $unit;
	protected $thumbnail;

	public function __construct() {
		$this->farmerId = $this->foreignKey( new Profile, true );
		$this->ingredient = $this->charField( 40 );
		$this->price = $this->decimalField( 3, 2 );
		$this->unit = $this->charField( 4 );
		$this->thumbnail = $this->charField( 255, true );
		parent::__construct();
	}
}