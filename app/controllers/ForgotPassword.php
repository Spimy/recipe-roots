<?php

class ForgotPassword {
	use Controller;

	public function index() {
		$data = [];

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$email = $_POST['email'];

			$userModel = new User();
			$user = $userModel->findOne( [ 'email' => $email ] );

			// This message needs to be send regardless of whether the user is found for security reasons
			// We do not want to let people know whether an email was registered or not because it'll expose that email
			$data['message'] = 'If the email is registered, then a password reset link will be sent to the email.';

			if ( ! empty( $user ) ) {
				$resetTokenModel = new ResetToken();

				$token = bin2hex( random_bytes( 64 ) );
				$resetTokenModel->create( [ 
					'userId' => $user['id'],
					'token' => hash( 'sha256', $token ),
					'expiresAt' => date( 'Y-m-d H:i:s', time() + 1800 ) // expires after 30 minutes
				] );

				$link = ROOT . "/forgotpassword/reset?token={$token}";
				$subject = 'Recipe Roots - Password Reset';
				$message = "
				<p>
					Reset your password <a href='$link'>here</a>
					<br>
					<br>
					Alternatively, you can copy the following link if the hyperlink above does not work:
					<a href='$link'>$link</a>
					<br>
					<br>
					If you did not request for a password reset, you may ignore this email.
				</p>
				";

				$headers = "From: <williamlaw.3001@gmail.com>\r\n";
				$headers .= "Reply-To: williamlaw.3001@gmail.com\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\r\n";

				$success = mail( $email, $subject, $message, $headers );
				if ( ! $success ) {
					// Success message should not be displayed since something stopped the email from being sent
					unset( $data['message'] );

					// We instead need to display an error message
					$data['error'] = 'Something went wrong while sending the email. Please try again in a few minutes.';
				}
			}
		}

		$this->view( 'reset-password/forgot-password', $data );
	}

	public function reset() {
		$data = [];
		$resetTokenModel = new ResetToken();

		switch ( $_SERVER['REQUEST_METHOD'] ) {
			case 'GET':
				if ( empty( $_GET['token'] ) ) {
					$data['error'] = 'No reset password token provided';
					$data['show'] = false;
					break;
				}

				$token = $_GET['token'];
				$tokenHash = hash( 'sha256', $token );
				$resetToken = $resetTokenModel->findOne( [ 'token' => $tokenHash ] );

				if ( empty( $resetToken ) ) {
					$data['error'] = 'Invalid reset password token';
					$data['show'] = false;
					break;
				}

				if ( strtotime( $resetToken['expiresAt'] ) <= time() ) {
					$data['error'] = 'Reset password token expired';
					$data['show'] = false;
					break;
				}

				$data['token'] = $token;
				$data['show'] = true;
				break;

			case 'POST':
				$data['show'] = true;
				$token = $_POST['token'];

				if ( empty( $_GET['token'] ) ) {
					$data['error'] = 'No reset password token provided';
					$data['show'] = false;
					break;
				}

				$tokenHash = hash( 'sha256', $token );
				$resetToken = $resetTokenModel->findOne( [ 'token' => $tokenHash ] );

				if ( empty( $resetToken ) ) {
					$data['error'] = 'Invalid reset password token';
					$data['show'] = false;
					break;
				}

				if ( strtotime( $resetToken['expiresAt'] ) <= time() ) {
					$data['error'] = 'Reset password token expired';
					$data['show'] = false;
					break;
				}

				$userModel = new User();
				$password = $_POST['password'];
				$confirmPassword = $_POST['confirmPassword'];

				if ( ! $userModel->validatePassword( $password, $confirmPassword ) ) {
					$data['error'] = 'Passwords do not match';
					break;
				}

				$success = $userModel->update(
					$resetToken['userId'],
					[ 
						'password' => password_hash( $password, PASSWORD_DEFAULT )
					]
				);

				if ( ! $success ) {
					$data['error'] = 'Something went wrong while resetting your password. Please try again later.';
					break;
				}

				// Let user know that password has been reset and change expiry to current time to mark it as expired or 'used'
				$data['message'] = 'Your password has been reset successfully.';
				$resetTokenModel->update( $resetToken['id'], [ 'expiresAt' => time() ] );
				break;
		}

		$this->view( 'reset-password/reset', $data );
	}

}