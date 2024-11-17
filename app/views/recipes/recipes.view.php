<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/browse.css">
	<title>Recipe Roots - Recipes</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header>
			<div class="heading">
				<h1>Your Recipes</h1>
				<a href="<?= ROOT ?>/recipes/create" class="btn btn--add">Create</a>
			</div>

			<form class="filter" method="GET">
				<div class="filter__input">
					<input type="text" name="filter" id="filter">
					<button class="filter__input__submit" type="submit">
						<img src="<?= ROOT ?>/assets/icons/search.svg" alt="search icon">
					</button>
				</div>

				<div class="filter__input">
					<select class="btn btn--invert" name="dietary" id="dietary">
						<option value="none" selected disabled>Dietary</option>
						<option value="none">None</option>
						<option value="vegetarian">Vegetarian</option>
						<option value="vegan">Vegan</option>
						<option value="halal">Halal</option>
						<!-- This is here so that the select box is long enough to also include the arrow -->
						<option value="vegetarian" hidden>Vegetarian long</option>
					</select>

					<button class="btn btn--invert btn--error" type="reset">Clear</button>
				</div>
			</form>
		</header>

		<section>
			<?php foreach ( $recipes as $recipe ) : ?>
				<h2><?= $recipe['id'] ?> => <?= $recipe['userId'] ?></h2>
			<?php endforeach; ?>
		</section>

	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>