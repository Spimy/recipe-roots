<?php

class Signin {
	use Controller;

	public function index() {
		$data = [];

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$userModel = new User();
			$profileModel = new Profile();

			$user = $userModel->verifyLogin( $_POST );

			if ( ! empty( $user ) ) {
				$profile = $profileModel->findOne( [ 'userId' => $user['id'] ] );
				$profile['dietaryType'] = $user['dietaryType'];
				$_SESSION['profile'] = $profile;

				$rememberMe = isset( $_POST['rememberMe'] ) && $_POST['rememberMe'] == true;
				if ( $rememberMe ) {
					setcookie( 'profile', base64_encode( json_encode( $profile ) ), [ 
						'expires' => time() + 30 * 24 * 60 * 60, // 30 days in seconds
						'path' => '/',
						'domain' => DOMAIN,
						'secure' => true,
						'httponly' => true,
					] );
				}

				redirect( $_GET['next'] ?? '' );
			}

			$data['error'] = 'Invalid email or password';
		}

		$this->view( 'signin', $data );
	}
}