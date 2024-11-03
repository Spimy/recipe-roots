<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/signup.css">
	<title>Recipe Roots - Sign Up</title>
</head>

<body>
	<?php include 'layout/header.php' ?>

	<main>
		<h1>Sign Up</h1>

		<?php if ( isset( $errors ) && count( $errors ) > 0 ) : ?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) : ?>
					<li class="errors__message"><?= $error ?></li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>

		<form class="signup" method="post">
			<div class="signup__input">
				<label>You are a</label>

				<div class="signup__input--radio signup__input--type">
					<label for="user"><input type="radio" name="accountType" id="user" value="user" required>User</label>
					<label for="farmer"><input type="radio" name="accountType" id="farmer" value="farmer">Farmer</label>
				</div>
			</div>

			<div class="signup__input">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" required>
			</div>

			<div class="signup__input">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" required>
			</div>

			<div class="signup__input">
				<label for="password">Password</label>
				<input type="password" name="password" id="password" required>
			</div>

			<div class="signup__input">
				<label for="confirm-password">Confirm Password</label>
				<input type="password" name="confirmPassword" id="confirm-password" required>
			</div>

			<div class="signup__input">
				<label>Dietary Type</label>

				<div class="signup__input--radio">
					<label for="none"><input type="radio" name="dietaryType" id="none" value="none" required>None</label>
					<label for="vegetarian">
						<input type="radio" name="dietaryType" id="vegetarian" value="vegetarian">
						Vegetarian
					</label>
					<label for="vegan"><input type="radio" name="dietaryType" id="vegan" value="vegan">Vegan</label>
					<label for="halal"><input type="radio" name="dietaryType" id="halal" value="halal">Halal</label>
				</div>
			</div>

			<button type="submit" class="btn">Sign Up</button>

			<p>Already have an account? <a href="<?= ROOT ?>/signin">Sign In</a></p>
		</form>

	</main>

	<?php include 'layout/footer.php' ?>
</body>

</html>