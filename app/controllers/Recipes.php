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
		$recipe = new Recipe();

		if ( $id === '' ) {
			return $this->view(
				'recipes/recipes',
				[ 
					'recipes' => $recipe->findAll( [ 'profileId' => $this->profile['id'] ], join: true ),
				]
			);
		}

		if ( ! is_numeric( $id ) || $id <= 0 ) {
			http_response_code( 404 );
			$this->view( '404' );
			die;
		}

		show( $recipe->findById( $id, true ) );

		$this->view(
			'recipes/recipe-detail',
			[ 
				'recipe' => $this->recipes[ $id - 1 ]
			]
		);
	}

	public function create() {
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$recipe = new Recipe();

			$errors = $recipe->validate( array_merge( $_POST, $_FILES ) );
			if ( count( $errors ) > 0 ) {
				http_response_code( 400 );
				$this->view(
					'recipes/recipe-editor',
					[ 
						'action' => 'Create',
						'errors' => $errors,
						'data' => $_POST
					] );
				die;
			}

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

			$amounts = $_POST['amounts'];
			$units = $_POST['units'];
			$ingredients = $_POST['ingredients'];

			$ingredientList = [];
			foreach ( $ingredients as $index => $ingredient ) {
				$ingredientList[] = [ 
					'ingredient' => $ingredient,
					'unit' => $units[ $index ],
					'amount' => $amounts[ $index ]
				];
			}

			$newRecipe['ingredients'] = json_encode( $ingredientList );
			$recipe->create( $newRecipe );
			redirect( 'recipes' );
		}

		$this->view( 'recipes/recipe-editor', [ 'action' => 'Create' ] );
	}

	public function edit( string $id = '' ) {
		if ( $id === '' || ! is_numeric( $id ) || $id <= 0 ) {
			http_response_code( 404 );
			$this->view( '404' );
			die;
		}

		$this->view(
			'recipes/recipe-editor',
			[ 
				'action' => 'Edit',
				'recipe' => $this->recipes[ $id - 1 ]
			]
		);
	}
}