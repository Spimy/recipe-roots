<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/forgot-password.css">
	<title>Recipe Roots - Forgot Password</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Reset Password</h1>

		<?php if ( ! empty( $message ) ) : ?>
			<p class="success"><?= $message ?></p>
			<p>Click <a href="<?= ROOT ?>/signin">here</a> to sign in.</p>
		<?php else : ?>
			<?php if ( ! empty( $error ) ) : ?>
				<p class="errors"><?= $error ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $show ) && $show ) : ?>
				<form class="forgot-password" method="post">
					<input type="hidden" name="token" value="<?= escape( $token ) ?>">

					<div class="forgot-password__input">
						<label for="password">New Password</label>
						<input type="password" name="password" id="password" required>
					</div>

					<div class="forgot-password__input">
						<label for="confirm-password">Confirm Password</label>
						<input type="password" name="confirmPassword" id="confirm-password" required>
					</div>

					<button type="submit" class="btn">Reset Password</button>
				</form>
			<?php endif ?>
		<?php endif; ?>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>