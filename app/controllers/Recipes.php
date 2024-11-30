<?php

class Recipes {
	use Controller;

	private $recipes = [ 
		[ 
			'title' => 'Medium Rare Steak',
			'ingredients' => [ 'Meat', 'Onion', 'Salt', 'Pepper' ],
			'instructions' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quos sapiente unde consequuntur dignissimos expedita! Tempore nobis voluptatibus beatae, aspernatur et aliquam quos eligendi, illum magnam, alias nostrum voluptatum sunt rerum!',
			'comments' => [ 
				'John Doe' => [ 
					'content' => 'glass discussion cheese share discuss act into sell thousand customs card electricity ants house old heart shinning deer bag older rays putting ought born',
					'rating' => 4
				],
				'Jane Doe' => [ 
					'content' => 'came sell blew seven hat having sure line yard grain properly faster lake wealth passage hunt expression symbol buy somewhere eat nearby invented table',
					'rating' => 3
				]
			]
		],
		[ 
			'title' => 'Classic Carbonara Pasta',
			'ingredients' => [ 'Spaghetti', 'Onion', 'Salt', 'Pepper' ],
			'instructions' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quos sapiente unde consequuntur dignissimos expedita! Tempore nobis voluptatibus beatae, aspernatur et aliquam quos eligendi, illum magnam, alias nostrum voluptatum sunt rerum!',
			'comments' => [ 
				'John Doe' => [ 
					'content' => 'glass discussion cheese share discuss act into sell thousand customs card electricity ants house old heart shinning deer bag older rays putting ought born',
					'rating' => 4
				]
			]
		]
	];

	private $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		// TODO: If profile is not a 'user' profile but a 'farmer' profile, ask to create a new profile

		handleInvalidCsrfToken( $this );
	}

	public function index( string $id = '' ) {
		$recipeModel = new Recipe();

		// List of all recipes for the authenticated user are controlled below
		if ( $id === '' ) {
			$recipeParams = [];

			if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
				$recipeParams = [ 
					"title" => "%" . $_GET['filter'] . "%",
					"ingredients" => "%" . $_GET['filter'] . "%",
					"instructions" => "%" . $_GET['filter'] . "%"
				];
			}

			$itemsPerPage = 1;
			$currentPage = isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) ? (int) $_GET['page'] : 1;
			$offset = ( $currentPage - 1 ) * $itemsPerPage;

			$totalRecipes = count( $recipeModel->findAll( [ 'profileId' => $this->profile['id'] ], contain: $recipeParams ) );
			$totalPages = floor( $totalRecipes / $itemsPerPage );
			$totalPages = $totalPages == 0 ? 1 : $totalPages;

			$recipes = $recipeModel->findAll(
				[ 'profileId' => $this->profile['id'] ],
				contain: $recipeParams,
				join: true,
				limit: $itemsPerPage,
				offset: $offset
			);

			$recipes = array_map(
				function ($recipe) {
					$commentModel = new Comment();
					$comments = $commentModel->findAll( [ 'recipeId' => $recipe['id'] ] );
					$averageRating = array_reduce( $comments, fn( $carry, $comment ) => $carry + $comment['rating'], 0 ) / ( count( $comments ) || 1 );
					return [ ...$recipe, "rating" => round( $averageRating, 0 ) ];
				},
				$recipes );

			return $this->view(
				'recipes/recipes',
				[ 
					'recipes' => $recipes,
					'currentPage' => $currentPage,
					'totalPages' => $totalPages,
				]
			);
		}

		// Detailed recipe page are controlled below
		$recipe = $recipeModel->findById( $id, true );
		if ( ! $recipe ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		$commentModel = new Comment();

		// Posting comments are POST requests so we need to handle that here
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$errors = $commentModel->validate( $_POST );
			if ( count( $errors ) > 0 ) {
				// TODO: handle errors
			}

			$commentModel->create( [ ...$_POST, 'recipeId' => $recipe['id'], 'profileId' => $this->profile['id'] ] );
		}

		$comments = $commentModel->findAll( [ 'recipeId' => $recipe['id'] ], join: true );
		$this->view(
			'recipes/recipe-detail',
			[ 'recipe' => $recipe, 'comments' => $comments ]
		);
	}

	private function formatIngredients( $amounts, $units, $ingredients ) {
		$ingredientList = [];

		if ( ! $amounts || ! $units || ! $ingredients ) {
			return $ingredientList;
		}

		foreach ( $ingredients as $index => $ingredient ) {
			$ingredientList[] = [ 
				'ingredient' => $ingredient,
				'unit' => $units[ $index ],
				'amount' => $amounts[ $index ]
			];
		}

		return $ingredientList;
	}

	private function handleErrors( $errors, $action, $id = null ) {
		if ( count( $errors ) > 0 ) {
			$ingredientList = $this->formatIngredients( $_POST['amounts'] ?? null, $_POST['units'] ?? null, $_POST['ingredients'] ?? null );
			$_POST['ingredients'] = $ingredientList;

			if ( $id ) {
				$_POST['id'] = $id;
			}

			http_response_code( 400 );
			$this->view(
				'recipes/recipe-editor',
				[ 
					'action' => $action,
					'errors' => $errors,
					'data' => $_POST
				] );
			die;
		}
	}

	/**
	 * Controller for create recipe page
	 * @return void
	 */
	public function create() {
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$recipe = new Recipe();

			$errors = $recipe->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Create' );

			$newRecipe = [ 
				'profileId' => $this->profile['id'],
				'title' => $_POST['title'],
				'prepTime' => $_POST['prepTime'] ?? null,
				'waitingTime' => $_POST['waitingTime'] ?? null,
				'servings' => $_POST['servings'] ?? null,
				'public' => $_POST['public'] == 'yes' ? 1 : 0,
				'instructions' => $_POST['instructions'],
			];

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$newRecipe['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			$ingredientList = $this->formatIngredients( $_POST['amounts'], $_POST['units'], $_POST['ingredients'] );
			$newRecipe['ingredients'] = json_encode( $ingredientList );

			$recipe->create( $newRecipe );
			redirect( 'recipes' );
		}

		$this->view( 'recipes/recipe-editor', [ 'action' => 'Create' ] );
	}

	/**
	 * Controller for edit recipe page
	 * @param string $id - id of the recipe provided in the URL
	 * @return void
	 */
	public function edit( string $id = '' ) {
		if ( ! $id ) {
			redirect( 'recipes' );
		}

		$recipeModel = new Recipe();
		$recipe = $recipeModel->findById( $id, true );

		if ( ! $recipe ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( $recipe['profileId'] != $this->profile['id'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You do not have permissions to edit this recipe' ] );
		}

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$errors = $recipeModel->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Edit', $id );

			$recipeData = [ 
				'title' => $_POST['title'],
				'prepTime' => $_POST['prepTime'] ?? null,
				'waitingTime' => $_POST['waitingTime'] ?? null,
				'servings' => $_POST['servings'] ?? null,
				'public' => $_POST['public'] == 'yes' ? true : false,
				'instructions' => $_POST['instructions'],
			];

			$ingredientList = $this->formatIngredients( $_POST['amounts'], $_POST['units'], $_POST['ingredients'] );
			$recipeData['ingredients'] = json_encode( $ingredientList );

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$recipeData['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			if ( empty( $_FILES['thumbnail'] ) ) {
				$recipeData['thumbnail'] = '';
			}

			$recipeModel->update( $id, $recipeData );
			redirect( "recipes/$id" );
		}

		$recipe['ingredients'] = json_decode( $recipe['ingredients'], true );
		$recipe['public'] = $recipe['public'] ? 'yes' : 'no';
		$this->view(
			'recipes/recipe-editor',
			[ 
				'action' => 'Edit',
				'data' => $recipe
			]
		);
	}
}