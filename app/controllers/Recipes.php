<?php

class Recipes {
	use Controller;

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

			$recipeConditions = [ 'profileId' => $this->profile['id'] ];
			$totalRecipes = count( $recipeModel->findAll( $recipeConditions, contain: $recipeParams ) );
			$totalPages = floor( $totalRecipes / $itemsPerPage );
			$totalPages = $totalPages == 0 ? 1 : $totalPages;

			$recipes = $recipeModel->findAll(
				data: $recipeConditions,
				contain: $recipeParams,
				join: true,
				limit: $itemsPerPage,
				offset: $offset
			);

			$recipes = array_map(
				function ($recipe) {
					$commentModel = new Comment();
					$comments = $commentModel->findAll( [ 'recipeId' => $recipe['id'] ] );
					$numComments = count( $comments ) > 0 ? count( $comments ) : 1;
					$averageRating = array_reduce( $comments, fn( $carry, $comment ) => $carry + $comment['rating'], 0 ) / $numComments;
					return [ ...$recipe, "rating" => round( $averageRating, 0 ) ];
				},
				$recipes );

			return $this->view(
				'recipes/recipes',
				[ 
					'browse' => false,
					'recipes' => $recipes,
					'currentPage' => $currentPage,
					'totalPages' => $totalPages,
				]
			);
		}

		if ( ! is_numeric( $id ) ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		// Detailed recipe page are controlled below
		$recipe = $recipeModel->findById( $id, true );
		if ( ! $recipe ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( ! $recipe['public'] && $recipe['profileId'] !== $this->profile['id'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'This recipe is private and cannot be accessed' ] );
		}

		$commentModel = new Comment();

		$comments = $commentModel->findAll( [ 'recipeId' => $recipe['id'] ], join: true );
		$this->view(
			'recipes/recipe-detail',
			[ 
				'recipe' => $recipe,
				'comments' => $comments,
				'commentErrors' => $_SESSION['comment_errors'] ?? [],
				'recipeErrors' => $_SESSION['recipe_errors'] ?? []
			]
		);
		unset( $_SESSION['comment_errors'] );
		unset( $_SESSION['recipe_errors'] );
	}

	public function browse() {
		$recipeModel = new Recipe();

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

		$recipeConditions = [ 'public' => 1 ];
		$totalRecipes = count( $recipeModel->findAll( $recipeConditions, contain: $recipeParams ) );
		$totalPages = floor( $totalRecipes / $itemsPerPage );
		$totalPages = $totalPages == 0 ? 1 : $totalPages;

		$recipes = $recipeModel->findAll(
			data: $recipeConditions,
			contain: $recipeParams,
			join: true,
			limit: $itemsPerPage,
			offset: $offset
		);

		$recipes = array_map(
			function ($recipe) {
				$commentModel = new Comment();
				$comments = $commentModel->findAll( [ 'recipeId' => $recipe['id'] ] );
				$numComments = count( $comments ) > 0 ? count( $comments ) : 1;
				$averageRating = array_reduce( $comments, fn( $carry, $comment ) => $carry + $comment['rating'], 0 ) / $numComments;
				return [ ...$recipe, "rating" => round( $averageRating, 0 ) ];
			},
			$recipes );

		return $this->view(
			'recipes/recipes',
			[ 
				'browse' => true,
				'recipes' => $recipes,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
			]
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
			$recipeModel = new Recipe();

			$errors = $recipeModel->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Create' );

			$newRecipe = [ 
				'profileId' => $this->profile['id'],
				'title' => $_POST['title'],
				'prepTime' => empty( $_POST['prepTime'] ) ? null : $_POST['prepTime'],
				'waitingTime' => empty( $_POST['waitingTime'] ) ? null : $_POST['waitingTime'],
				'servings' => empty( $_POST['servings'] ) ? null : $_POST['servings'],
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

			$recipe = $recipeModel->create( $newRecipe );
			redirect( 'recipes/' . $recipe['id'] );
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

		if ( ! is_numeric( $id ) ) {
			http_response_code( 404 );
			return $this->view( '404' );
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
				'prepTime' => empty( $_POST['prepTime'] ) ? null : $_POST['prepTime'],
				'waitingTime' => empty( $_POST['waitingTime'] ) ? null : $_POST['waitingTime'],
				'servings' => empty( $_POST['servings'] ) ? null : $_POST['servings'],
				'public' => $_POST['public'] == 'yes' ? 1 : 0,
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

			$success = $recipeModel->update( $id, $recipeData );
			if ( ! $success ) {
				http_response_code( 500 );
				$_SESSION['recipe_errors'] = [ 'Something went wrong updating the recipe and could not be saved' ];
			}

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

	public function delete() {
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			redirect( 'recipes' );
		}

		if ( empty( $_POST['recipeId'] ) || ! is_numeric( $_POST['recipeId'] ) ) {
			http_response_code( 400 );
			redirect( 'recipes' );
		}

		$recipeModel = new Recipe();
		$recipeId = $_POST['recipeId'];

		$recipe = $recipeModel->findById( $recipeId );
		if ( ! $recipe ) {
			http_response_code( 404 );
			redirect( '404' );
		}

		if ( $recipe['profileId'] !== $this->profile['id'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You cannot delete a recipe that you do not own' ] );
		}

		$success = $recipeModel->delete( $recipeId );

		if ( $success ) {
			redirect( 'recipes' );
		}

		http_response_code( 500 );
		$_SESSION['recipe_errors'] = [ 'Recipe could not be deleted' ];
		redirect( "recipes/$recipeId" );
	}

	public function comment( string $method = null ) {
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			redirect( 'recipes' );
		}

		switch ( strtolower( $method ) ) {
			case 'add': {
				$commentModel = new Comment();
				$errors = $commentModel->validate( $_POST );

				if ( count( $errors ) > 0 ) {
					http_response_code( 400 );

					if ( isset( $errors['recipeId'] ) ) {
						redirect( 'recipes' );
					}

					$_SESSION['comment_errors'] = $errors;
					redirect( 'recipes/' . $_POST['recipeId'] . '#comments' );
				}

				$recipeId = $_POST['recipeId'];
				$recipeModel = new Recipe();
				$recipe = $recipeModel->findById( $recipeId, true );

				if ( ! $recipe ) {
					http_response_code( 404 );
					redirect( '404' );
				}

				$newComment = $commentModel->create( [ ...$_POST, 'recipeId' => $recipe['id'], 'profileId' => $this->profile['id'] ] );
				redirect( "recipes/$recipeId#comment-" . $newComment['id'] );
				break;
			}
			case 'edit': {
				$commentModel = new Comment();
				$errors = $commentModel->hasProvidedId( $_POST );

				if ( count( $errors ) > 0 ) {
					http_response_code( 400 );

					if ( isset( $errors['recipeId'] ) ) {
						redirect( 'recipes' );
					}


					$_SESSION['comment_errors'] = $errors;
					redirect( 'recipes/' . $_POST['recipeId'] . '#comments' );
				}

				$recipeId = $_POST['recipeId'];
				$commentId = $_POST['commentId'];

				$errors = $commentModel->hasProvidedContent( $_POST );
				if ( count( $errors ) > 0 ) {
					http_response_code( 400 );
					$_SESSION['comment_errors'] = $errors;
					redirect( "recipes/$recipeId#comment-$commentId" );
				}

				$commentModel->update( $commentId, $_POST );
				redirect( "recipes/$recipeId#comment-$commentId" );
				break;
			}
			case 'delete': {

			}
			default: {
				redirect( 'recipes' );
			}
		}
	}
}