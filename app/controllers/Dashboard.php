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
		$sales = $purchaseModel->findAll( [ 'farmerId' => $this->profile['id'] ] );
		$groupedSales = $purchaseModel->groupSalesByDate( $sales );
		$dataPoints = $purchaseModel->createDataPoints( $groupedSales );

		// $dataPoints = [ 
		// 	[ 'y' => 320, 'label' => 'Oct 2024' ],
		// 	[ 'y' => 560, 'label' => 'Nov 2024' ],
		// 	[ 'y' => 200, 'label' => 'Nov 2024' ],
		// 	[ 'y' => 120, 'label' => 'Dec 2024' ],
		// ];
		$this->view( 'farmer/dashboard', [ 'dataPoints' => $dataPoints ] );
	}
}