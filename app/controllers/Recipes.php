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
	}

	public function index( string $id = '' ) {
		$recipeModel = new Recipe();

		if ( $id === '' ) {
			return $this->view(
				'recipes/recipes',
				[ 
					'recipes' => $recipeModel->findAll( [ 'profileId' => $this->profile['id'] ], join: true ),
				]
			);
		}

		$recipe = $recipeModel->findById( $id, true );
		if ( ! $recipe ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		$this->view(
			'recipes/recipe-detail',
			[ 'recipe' => $recipe ]
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

	private function handleErrors( $errors, $action ) {
		if ( count( $errors ) > 0 ) {
			$ingredientList = $this->formatIngredients( $_POST['amounts'] ?? null, $_POST['units'] ?? null, $_POST['ingredients'] ?? null );
			$_POST['ingredients'] = $ingredientList;

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
				'public' => $_POST['public'] == 'yes' ? true : false,
				'instructions' => $_POST['instructions'],
			];

			if ( isset( $_FILES['thumbnail'] ) ) {
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

	public function edit( string $id = '' ) {
		$recipeModel = new Recipe();
		$recipe = $recipeModel->findById( $id, true );

		if ( ! $recipe ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$errors = $recipeModel->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Edit' );

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

			show( $_FILES['thumbnail'] );

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$recipeData['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			if ( empty( $_FILES['thumbnail'] ) ) {
				$recipeData['thumbnail'] = '';
			}

			show( $recipeData );

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