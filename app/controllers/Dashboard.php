<?php

class Dashboard {
	use Controller;

	private $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		if ( $this->profile['type'] !== PROFILE_TYPES['farmer'] ) {
			http_response_code( 403 );
			redirect( "settings/profiles?next=" . $_GET['url'] );
		}

		handleInvalidCsrfToken( $this );
	}

	public function index() {
		$purchaseModel = new Purchase();
		$sales = $purchaseModel->findAll( [ 'farmerId' => $this->profile['id'] ], join: true );
		$groupedSales = $purchaseModel->groupSalesByDate( $sales );
		$dataPoints = $purchaseModel->createDataPoints( $groupedSales );

		// $dataPoints = [ 
		// 	[ 'y' => 320, 'label' => 'Oct 2024' ],
		// 	[ 'y' => 560, 'label' => 'Nov 2024' ],
		// 	[ 'y' => 200, 'label' => 'Nov 2024' ],
		// 	[ 'y' => 120, 'label' => 'Dec 2024' ],
		// ];

		$itemsPerPage = 6;
		$currentPage = isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) ? (int) $_GET['page'] : 1;
		$offset = ( $currentPage - 1 ) * $itemsPerPage;
		$ingredientConditions = [ 'farmerId' => $this->profile['id'] ];

		$ingredientModel = new Ingredient();
		$totalRecipes = count( $ingredientModel->findAll( $ingredientConditions ) );
		$totalPages = ceil( $totalRecipes / $itemsPerPage );
		$totalPages = $totalPages == 0 ? 1 : $totalPages;

		$ingredients = $ingredientModel->findAll(
			data: $ingredientConditions,
			join: true,
			limit: $itemsPerPage,
			offset: $offset
		);

		$this->view(
			'farmer/dashboard',
			[ 
				'dataPoints' => $dataPoints,
				'ingredients' => $ingredients,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
			]
		);
	}

	public function produce( string $method = null, int $id = null ) {
		if ( ! $method ) {
			redirect( 'dashboard' );
		}

		switch ( $method ) {
			case 'add': {
				$this->addProduce();
				break;
			}
		}
	}

	protected function addProduce() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$ingredientModel = new Ingredient();
			$errors = $ingredientModel->validate( array_merge( $_POST, $_FILES ) );

			if ( count( $errors ) > 0 ) {
				http_response_code( 400 );
				return $this->view( 'farmer/produce-editor', [ 'action' => 'Add', 'errors' => $errors ] );
			}

			$newIngredient = [ 
				'farmerId' => $this->profile['id'],
				'ingredient' => $_POST['ingredient'],
				'price' => number_format( $_POST['price'], 2, '.', '' ),
				'unit' => $_POST['unit'],
			];

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$newIngredient['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			$ingredientModel->create( $newIngredient );
			redirect( 'dashboard' );
		}
		$this->view( 'farmer/produce-editor', [ 'action' => 'Add' ] );
	}
}