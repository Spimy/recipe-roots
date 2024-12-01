<?php

class Recipe extends Model {
	protected $profileId;
	protected $title;
	protected $thumbnail;
	protected $prepTime;
	protected $waitingTime;
	protected $servings;
	protected $public;
	protected $dietaryType;
	protected $ingredients;
	protected $instructions;

	public function __construct() {
		$this->profileId = $this->foreignKey( new Profile, true );
		$this->title = $this->charField( 100 );
		$this->thumbnail = $this->charField( 255, true );
		$this->prepTime = $this->integerField( true );
		$this->waitingTime = $this->integerField( true );
		$this->servings = $this->integerField( true );
		$this->public = $this->booleanField( false );
		$this->dietaryType = $this->charField( 10, true, false );
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

			$fileTmpPath = $_FILES['thumbnail']['tmp_name'];
			$fileName = $data['thumbnail']['name'];

			$allowedExtensions = [ 'jpg', 'jpeg', 'png', 'gif' ];
			$allowedMimeTypes = [ 'image/jpeg', 'image/png', 'image/gif' ];

			$fileExtension = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
			if ( ! in_array( $fileExtension, $allowedExtensions ) ) {
				$errors['thumbnailExt'] = 'Invalid file extension for thumbnail. Allowed extensions are: ' . implode( ', ', $allowedExtensions );
			}

			$fileMimeType = mime_content_type( $fileTmpPath );
			if ( ! in_array( $fileMimeType, $allowedMimeTypes ) ) {
				$errors['thumbnailMime'] = 'Invalid MIME type for thumbnail. Allowed types are: ' . implode( ', ', $allowedMimeTypes );
			}

			$imageSize = @getimagesize( $fileTmpPath );
			if ( $imageSize === false ) {
				$errors['thumbnailImg'] = "The thumbnail is not a valid image";
			}
		}

		if ( ! empty( $data['prepTime'] ) && ! is_numeric( $data['prepTime'] ) ) {
			$errors['prepTime'] = 'Preparation time must be a number in minutes';
		}
		if ( ! empty( $data['waitingTime'] ) && ! is_numeric( $data['waitingTime'] ) ) {
			$errors['waitingTime'] = 'Waiting time must be a number in minutes';
		}

		if ( ! empty( $data['servings'] ) && ! is_numeric( $data['servings'] ) ) {
			$errors['servings'] = 'Servings must be a number';
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

			if ( count( array_filter( $data['amounts'], fn( $amount ) => ! is_numeric( $amount ) ) ) > 0 ) {
				$errors['amounts'] = 'Ingredient amount detail should be a number';
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