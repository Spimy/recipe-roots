<?php

class Ingredients {
	use Controller;

	private $profile;
	private $itemsPerPage = 6;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		if ( $this->profile['type'] !== PROFILE_TYPES['user'] ) {
			http_response_code( 403 );
			redirect( "settings/profiles?next=" . $_GET['url'] );
		}

		handleInvalidCsrfToken( $this );
	}

	public function index() {
		$ingredientParams = [];

		if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
			$ingredientParams = [ 'ingredient' => "%" . $_GET['filter'] . "%" ];
		}

		$itemsPerPage = 6;
		$ingredientModel = new Ingredient();

		[ $currentPage, $totalPages, $offset ] = getPaginationData(
			$ingredientModel,
			$itemsPerPage,
			$ingredientParams
		);

		$ingredients = $ingredientModel->findAll(
			contain: $ingredientParams,
			join: true,
			limit: $itemsPerPage,
			offset: $offset
		);

		$this->view(
			'ingredients',
			[ 
				'ingredients' => $ingredients,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
			]
		);
	}
}