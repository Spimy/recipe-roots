<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/settings.css">
	<title>Recipe Roots - Settings</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Settings</h1>

		<article class="settings">
			<aside class="settings__nav">
				<ul role="list" class="settings__nav__links">
					<li><a class="settings__nav__links__link" href="<?= ROOT ?>/settings">Account</a></li>
					<li>
						<a class="settings__nav__links__link settings__nav__links__link--active"
							href="<?= ROOT ?>/settings/profiles">
							Profiles
						</a>
					</li>
				</ul>

				<div class="settings__nav__btns">
					<a class="btn btn--error" href="<?= ROOT ?>/signout">Sign Out</a>
				</div>
			</aside>

			<div class="settings__forms">
				<form class="settings__editor" method="post">
					<div class="settings__editor__input">
						<label for="email">Email</label>
						<input type="email" name="email" id="email" required>
					</div>

					<div class="settings__editor__input">
						<label for="currentPassword">Current Password</label>
						<input type="password" name="currentPassword" id="currentPassword" required>
					</div>

					<div class="settings__editor__input">
						<label for="newPassword">New Password</label>
						<input type="password" name="newPassword" id="newPassword">
					</div>

					<div class="settings__editor__input">
						<label for="confirmPassword">Confirm New Password</label>
						<input type="password" name="confirmPassword" id="confirmPassword">
					</div>

					<button type="submit" class="btn">Save</button>
				</form>
			</div>
		</article>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>