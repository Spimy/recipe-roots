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

	public function index( string $id = '' ) {
		$recipe = new Recipe();

		if ( $id === '' ) {
			// $recipe->create( [ 'userId' => 1 ] );

			echo "<pre>";
			print_r( $recipe->findAll( join: true ) );
			echo "</pre>";

			return $this->view(
				'recipes/recipes',
				[ 
					'recipes' => [ 'Medium Rare Steak', 'Classic Carbonara Pasta', 'Alfredo Pasta' ]
				]
			);
		}

		if ( ! is_numeric( $id ) || $id <= 0 ) {
			http_response_code( 404 );
			$this->view( '404' );
			die;
		}


		echo "<pre>";
		print_r( $recipe->findById( $id, true ) );
		echo "</pre>";

		$this->view(
			'recipes/recipe-detail',
			[ 
				'recipe' => $this->recipes[ $id - 1 ]
			]
		);
	}


	public function edit( string $id = '' ) {
		if ( $id === '' || ! is_numeric( $id ) || $id <= 0 ) {
			http_response_code( 404 );
			$this->view( '404' );
			die;
		}

		$this->view(
			'recipes/recipe-edit',
			[ 
				'recipe' => $this->recipes[ $id - 1 ]
			]
		);
	}
}