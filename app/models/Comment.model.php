<?php

class Comment extends Model {
	protected $profileId;
	protected $recipeId;
	protected $content;
	protected $rating;

	public function __construct() {
		$this->profileId = $this->foreignKey( new Profile(), true );
		$this->recipeId = $this->foreignKey( new Recipe(), true );
		$this->content = $this->textField();
		$this->rating = $this->integerField();
		parent::__construct();
	}

	public function validate( $data ) {
		$errors = [];

		if ( empty( $data['recipeId'] ) ) {
			$errors['recipeId'] = 'No recipe id has been provided';
		}

		if ( ! is_numeric( $data['recipeId'] ) ) {
			$errors['recipeId'] = 'Invalid recipe id provided';
		}

		if ( empty( $data['content'] ) ) {
			$errors['content'] = 'Your comment has no content';
		}

		if ( empty( $data['rating'] ) ) {
			$errors['rating'] = 'A rating is required';
		}

		if ( ! is_numeric( $data['rating'] ) ) {
			$errors['rating'] = 'Invalid rating value';
		}

		return $errors;
	}
}