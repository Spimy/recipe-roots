<?php

class Home {
	use Controller;

	public function index() {
		$this->view(
			'home',
			[ 
				'fruits' => [ 'apple', 'banana', 'cherry' ],
			]
		);

		$user = new User();
		try {
			$user->create( [ 'username' => 'ayaka' ] );
		} catch (PDOException $e) {
			// https://dev.mysql.com/doc/mysql-errors/8.0/en/server-error-reference.html
			// IDs still get auto incremented even if there is an error
			if ( $e->errorInfo[1] === 1062 ) {
				echo 'Username already taken!';
			}
		}

		echo "<pre>";
		print_r( $user->findAll() );
		echo "</pre>";

		echo "<pre>";
		print_r( $user->findById( 1 ) );
		echo "</pre>";
	}
}