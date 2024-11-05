<?php

class Signup {
	use Controller;

	public function index() {
		$data = [];

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$user = new User();
			$profile = new Profile();

			$errors = array_merge( $user->validate( $_POST ), $profile->validate( $_POST ) );
			if ( count( $errors ) == 0 ) {
				$newUser = $user->create( [ 
					'email' => $_POST['email'],
					'password' => password_hash( $_POST['password'], PASSWORD_DEFAULT ),
					'dietaryType' => $_POST['dietaryType'] == 'none' ? null : $_POST['dietaryType'],
				] );

				$profile->create( [ 
					'userId' => $newUser['id'],
					'username' => $_POST['username'],
					'accountType' => $_POST['accountType'] == 'user' ? PROFILE_TYPES['user'] : PROFILE_TYPES['farmer']
				] );

				$_SESSION['signup'] = 'Successfully signed up. Proceed by signing in.';
				redirect( 'signin' );
			}

			$data['errors'] = $errors;
		}

		$this->view( 'signup', $data );
	}
}