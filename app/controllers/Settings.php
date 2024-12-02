<?php

class Settings {
	use Controller;

	private $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		handleInvalidCsrfToken( $this );
	}

	// Handle user settings
	public function index() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = [];

			if ( isset( $_SESSION['accountDeleteError'] ) ) {
				http_response_code( $_SESSION['accountDeleteError']['status'] );
				$errors['accountDeleteError'] = $_SESSION['accountDeleteError']['message'];
			}

			$currentPassword = $_POST['currentPassword'] ?? null;
			if ( ! $currentPassword ) {
				http_response_code( 400 );
				$errors['currentPassword'] = 'Current password is required';

				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			$email = $_POST['email'] ?? null;
			if ( ! $email ) {
				http_response_code( 400 );
				$errors['email'] = 'Email is required';
			}

			$userModel = new User();
			$user = $userModel->findById( $this->profile['user']['id'] );

			if ( ! password_verify( $currentPassword, $user['password'] ) ) {
				http_response_code( 403 );
				$errors['currentPassword'] = 'The password you provided is incorrect';
				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			if ( $email !== $user['email'] && count( $userModel->findOne( [ 'email' => $email ] ) ) > 0 ) {
				http_response_code( 400 );
				$errors['email'] = 'The email you provided is already in use by another account';
			}

			$newPassword = $_POST['newPassword'] ?? null;
			$confirmPassword = $_POST['confirmPassword'] ?? null;

			if ( $newPassword && $newPassword !== $confirmPassword ) {
				http_response_code( 400 );
				$errors['newPassword'] = 'The new passwords do not match';
			}

			if ( count( $errors ) > 0 ) {
				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
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
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => [ 'Something went wrong and could not update your information' ]
					]
				);
			}

			$profile = ( new Profile() )->findById( $this->profile['id'], join: true );
			$_SESSION['profile'] = $profile;

			return $this->view(
				'settings/account',
				[ 
					'account' => $profile['user'],
					'message' => 'Your information has been updated'
				]
			);
		}

		$this->view( 'settings/account', [ 'account' => $this->profile['user'] ] );
	}

	// Handle profile settings
	public function profiles() {
		$this->view( 'settings/profile', [ 'profile' => $this->profile ] );
	}

	// Handle delete account
	public function delete() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = [];

			$currentPassword = $_POST['currentPassword'] ?? null;
			if ( ! $currentPassword ) {
				http_response_code( 400 );
				$errors['currentPassword'] = 'Current password is required';

				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			$userModel = new User();
			$user = $userModel->findById( $this->profile['user']['id'] );

			if ( ! password_verify( $currentPassword, $user['password'] ) ) {
				http_response_code( 403 );
				$errors['currentPassword'] = 'The password you provided is incorrect';
				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			// Also deletes profiles associated as they cascade once user is deleted
			$success = $userModel->delete( $user['id'] );
			if ( ! $success ) {
				http_response_code( 500 );
				$_SESSION['accountDeleteError'] = [ 
					'status' => 500,
					'message' => 'Something went wrong when deleting your account, please try again'
				];
				redirect( 'settings' );
			}

			// Sign the user out after the account is deleted
			redirect( 'signout' );
		}
	}
}