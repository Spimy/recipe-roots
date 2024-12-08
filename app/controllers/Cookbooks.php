<?php

class Cookbooks {
	use Controller;

	protected $profile;

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
		$cookbookParams = [];
		if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
			$cookbookParams = [ 
				"title" => "%" . $_GET['filter'] . "%",
				"description" => "%" . $_GET['filter'] . "%"
			];
		}

		[ $currentPage, $totalPages, $cookbooks ] = getPaginationData(
			new Cookbook,
			6,
			[ 'profileId' => $this->profile['id'] ],
			$cookbookParams
		);
		$this->view(
			'cookbooks/cookbooks',
			[ 
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'cookbooks' => $this->getRating( $cookbooks ),
				'browse' => false
			]
		);
	}

	public function browse() {
		$cookbookParams = [];
		if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
			$cookbookParams = [ 
				"title" => "%" . $_GET['filter'] . "%",
				"description" => "%" . $_GET['filter'] . "%"
			];
		}

		[ $currentPage, $totalPages, $cookbooks ] = getPaginationData(
			new Cookbook,
			6,
			$this->profile['user']['isAdmin'] ? [] : [ 'public' => 1 ],
			$cookbookParams
		);
		$this->view(
			'cookbooks/cookbooks',
			[ 
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'cookbooks' => $this->getRating( $cookbooks ),
				'browse' => true
			]
		);
	}

	public function create() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$cookbookModel = new Cookbook();
			$errors = $cookbookModel->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Create' );

			$newCookbook = [ 
				'profileId' => $this->profile['id'],
				'title' => $_POST['title'],
				'description' => $_POST['description'],
				'public' => $_POST['public'] == 'yes' ? 1 : 0
			];

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$newCookbook['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			$cookbook = $cookbookModel->create( $newCookbook );
			redirect( 'cookbooks/' . $cookbook['id'] );
		}

		$this->view( 'cookbooks/cookbook-editor', [ 'action' => 'Create' ] );
	}

	private function handleErrors( $errors, $action, $id = null ) {
		if ( count( $errors ) > 0 ) {
			if ( $id ) {
				$_POST['id'] = $id;
			}

			http_response_code( 400 );
			$this->view(
				'cookbooks/cookbook-editor',
				[ 
					'action' => $action,
					'errors' => $errors,
					'data' => $_POST
				] );
			die;
		}
	}

	private function getRating( array $cookbooks ) {
		$cookbookJoinModel = new CookbookJoin();
		$commentModel = new Comment();

		foreach ( $cookbooks as $index => $cookbook ) {
			$cookbooks[ $index ]['rating'] = 0;
			$numRatings = 0;
			$totalRating = 0;

			$joins = $cookbookJoinModel->findAll( [ 'cookbookId' => $cookbook['id'] ] );

			foreach ( $joins as $join ) {
				$comments = $commentModel->findAll( [ 'recipeId' => $join['recipeId'] ] );
				$numRatings += count( $comments );
				$totalRating += array_reduce( $comments, fn( $ca, $c ) => $ca + $c['rating'], 0 );
			}

			$cookbooks[ $index ]['rating'] = $totalRating / ( $numRatings > 0 ? $numRatings : 1 );
		}

		return $cookbooks;
	}
}