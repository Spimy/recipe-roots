<?php

class Purchase extends Model {
	protected $userProfileId;
	protected $farmerId;
	protected $ingredientId;
	protected $amount;

	public function __construct() {
		$this->userProfileId = $this->foreignKey( new Profile, true );
		$this->farmerId = $this->foreignKey( new Profile, true );
		$this->ingredientId = $this->foreignKey( new Ingredient, true );
		$this->amount = $this->integerField();
		parent::__construct();
	}

	public function groupSalesByDate( array $sales ) {
		$groupedSales = [];

		foreach ( $sales as $sale ) {
			$month = ucfirst( strtotime( 'M', $sale['createdAt'] ) );
			$year = strtotime( 'YY', $sale['createdAt'] );
			$group = "$month $year";

			if ( ! isset( $groupedSales[ $group ] ) ) {
				$groupedSales[ $group ] = 0;
			}

			$groupedSales[ $group ] += $sale['amount'] * $sale['ingredient']['price'];
		}

		return $groupedSales;
	}

	public function createDataPoints( array $groupedSales ) {
		$dataPoints = [];

		foreach ( $groupedSales as $date => $revenue ) {
			$dataPoints[] = [ 
				'y' => $revenue,
				'label' => $date
			];
		}

		return $dataPoints;
	}
}