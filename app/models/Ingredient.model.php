<?php

class Ingredient extends Model {
	protected $farmerId;
	protected $ingredient;
	protected $price;
	protected $unit;

	public function __construct() {
		$this->farmerId = $this->foreignKey( new Profile, true );
		$this->ingredient = $this->charField( 40 );
		$this->price = $this->decimalField( 3, 2 );
		$this->unit = $this->charField( 4 );
		parent::__construct();
	}
}