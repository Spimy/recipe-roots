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
		[ $currentPage, $totalPages, $cookbooks ] = getPaginationData(
			new Cookbook,
			6,
			[ 'profileId' => $this->profile['id'] ]
		);
		$this->view(
			'cookbooks/cookbooks',
			[ 
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'cookbooks' => $cookbooks,
				'browse' => false
			]
		);
	}

	public function browse() {
		[ $currentPage, $totalPages, $cookbooks ] = getPaginationData(
			new Cookbook,
			6,
			$this->profile['user']['isAdmin'] ? [] : [ 'public' => 1 ]
		);
		$this->view(
			'cookbooks/cookbooks',
			[ 
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'cookbooks' => $cookbooks,
				'browse' => true
			]
		);
	}
}