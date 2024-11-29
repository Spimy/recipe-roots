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