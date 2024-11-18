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

	public function validate( $data ) {
		$errors = [];

		if ( empty( $data['title'] ) ) {
			$errors['title'] = 'Title is required';
		}

		if ( isset( $data['thumbnail'] ) ) {
			if ( $data['thumbnail']['error'] == UPLOAD_ERR_INI_SIZE ) {
				$errors['thumbnail'] = 'Thumbnail is too large, it should not exceed 2MB';
			}
		}

		if ( empty( $data['public'] ) ) {
			$errors['public'] = 'Specify whether the recipe is public or private';
		}

		if ( isset( $data['ingredients'] ) && isset( $data['units'] ) && isset( $data['amounts'] ) ) {
			if (
				count( $data['ingredients'] ) != count( $data['units'] )
				|| count( $data['ingredients'] ) != count( $data['amounts'] )
				|| count( $data['amounts'] ) != count( $data['units'] )
			) {
				$errors['ingredients'] = 'Ingredient details are not complete';
			}
		} else {
			$errors['ingredients'] = 'At least one ingredient is required';
		}

		if ( empty( $data['instructions'] ) ) {
			$errors['instructions'] = 'Instructions are required';
		}

		return $errors;
	}
}