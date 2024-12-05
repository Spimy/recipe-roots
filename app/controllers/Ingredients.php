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
				'errors' => $_SESSION['cartErrors'] ?? []
			]
		);
		unset( $_SESSION['cartErrors'] );
	}

	public function cart() {
		$cart = json_decode( $_COOKIE['cart'] ?? '[]', true );

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = [];

			if ( empty( $_POST['ingredientId'] ) ) {
				http_response_code( 400 );
				$errors[] = 'Ingredient id is required';
			}

			if ( empty( $_POST['amount'] ) || ! is_numeric( $_POST['amount'] ) ) {
				http_response_code( 400 );
				$errors[] = 'Amount is required and must be numeric';
			}

			if ( (int) $_POST['amount'] < 0 || (int) $_POST['amount'] > 99 ) {
				http_response_code( 400 );
				$errors[] = 'Amount must be between 0-99';
			}

			if ( count( $errors ) > 0 ) {
				$_SESSION['cartErrors'] = $errors;
				unset( $_GET['url'] );
				redirect( 'ingredients?' . http_build_query( $_GET ?? [] ) );
			}

			if ( (int) $_POST['amount'] === 0 ) {
				unset( $cart[ $_POST['ingredientId'] ] );
			} else {
				$cart[ $_POST['ingredientId'] ] = round( (int) $_POST['amount'], 0 );
			}

			setcookie( 'cart', count( $cart ) > 0 ? json_encode( $cart ) : '' );
			unset( $_GET['url'] );
			redirect( 'ingredients?' . http_build_query( $_GET ?? [] ) );
		}

		return $this->index();
	}
}