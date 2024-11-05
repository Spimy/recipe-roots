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
		<h1>Forgot Password</h1>

		<?php if ( ! empty( $message ) ) : ?>
			<p class="success"><?= $message ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $error ) ) : ?>
			<p class="errors"><?= $error ?></p>
		<?php endif ?>

		<form class="forgot-password" method="post">
			<div class="forgot-password__input">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" required>
			</div>

			<button type="submit" class="btn">Reset Password</button>

			<p>Remembered your password? <a href="<?= ROOT ?>/signin">Sign In</a></p>
		</form>

	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>