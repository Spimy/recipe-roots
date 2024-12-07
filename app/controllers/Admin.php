<?php

class Admin {
	use Controller;

	private $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		if ( ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			redirect( 'home' );
		}

		handleInvalidCsrfToken( $this );
	}


	public function index() {
		[ $currentPage, $totalPages, $users ] = getPaginationData( new User, 6 );
		$this->view( 'admin/users', [ 'users' => $users, 'currentPage' => $currentPage, 'totalPages' => $totalPages ] );
	}

	public function edit( $type = null, $id = null ) {
		if ( ! $id || ! is_numeric( $id ) ) {
			redirect( 'admin' );
		}

		$userModel = new User();
		$user = $userModel->findById( $id );

		if ( ! $user ) {
			redirect( 'admin' );
		}

		switch ( $type ) {
			case 'account': {
				return $this->editUser( $user );
			}
			case 'profiles': {

			}
			default: {
				return redirect( 'admin' );
			}
		}
	}

	private function editUser( $user ) {
		$userModel = new User();

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = [];

			$currentPassword = $_POST['currentPassword'] ?? null;
			if ( ! $currentPassword ) {
				http_response_code( 400 );
				$errors['currentPassword'] = 'Current password is required';

				return $this->view(
					'admin/edit-user',
					[ 
						'user' => $user,
						'errors' => $errors
					]
				);
			}

			$email = $_POST['email'] ?? null;
			if ( ! $email ) {
				http_response_code( 400 );
				$errors['email'] = 'Email is required';
			}

			$admin = $userModel->findById( $this->profile['userId'] );
			if ( ! password_verify( $currentPassword, $admin['password'] ) ) {
				http_response_code( 403 );
				$errors['currentPassword'] = 'The password you provided is incorrect';
				return $this->view(
					'admin/edit-user',
					[ 
						'user' => $user,
						'errors' => $errors
					]
				);
			}

			$newPassword = $_POST['newPassword'] ?? null;
			$confirmPassword = $_POST['confirmPassword'] ?? null;

			if ( $newPassword && $newPassword !== $confirmPassword ) {
				http_response_code( 400 );
				$errors['newPassword'] = 'The new passwords do not match';
			}

			if ( count( $errors ) > 0 ) {
				return $this->view(
					'admin/edit-user',
					[ 
						'user' => $user,
						'errors' => $errors
					]
				);
			}

			$userDetails = [ 
				'email' => $email,
				'dietaryType' => $_POST['dietaryType'] == 'none' ? null : $_POST['dietaryType'] ?? null,
			];

			if ( $newPassword ) {
				$userDetails = [ ...$userDetails, 'password' => password_hash( $newPassword, PASSWORD_DEFAULT ) ];
			}

			$success = $userModel->update( $user['id'], $userDetails );
			if ( ! $success ) {
				http_response_code( 500 );
				return $this->view(
					'admin/edit-user',
					[ 
						'user' => $user,
						'errors' => [ 'Something went wrong and could not update your information' ]
					]
				);
			}

			// If the user that got updated is the admin's account then we need to update their current session
			if ( $user['id'] === $admin['id'] ) {
				$profile = ( new Profile() )->findById( $this->profile['id'], join: true );
				$_SESSION['profile'] = $profile;
			}

			$user = $userModel->findById( $user['id'] );
			$message = 'Updated user successfully';
		}

		$this->view( 'admin/edit-user', [ 'user' => $user, 'message' => $message ?? null ] );
	}
}