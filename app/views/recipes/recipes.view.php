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
					<input type="text" name="filter" id="filter" value="<?= $_GET['filter'] ?? null ?>">
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
					</select>

					<button class="btn btn--invert btn--error" type="reset">Clear</button>
				</div>
			</form>
		</header>

		<section class="grid">
			<?php foreach ( $recipes as $recipe ) : ?>
				<article class="card">
					<img class="card__thumbnail" src="<?= $recipe['thumbnail'] ?>"
						alt="<?= extractTitleLetters( $recipe['title'] ) ?>">
					<div>
						<div class="card__head">
							<div class="card__head__title">
								<h2><?= $recipe['title'] ?></h2>
							</div>
							<div class="card__head__info">
								<?php for ( $i = 0; $i < min( $recipe['rating'], 5 ); $i++ ) : ?>
									<img src="<?= ROOT ?>/assets/icons/star-yellow.svg" alt="yellow star">
								<?php endfor ?>
								<?php for ( $i = min( $recipe['rating'], 5 ); $i < 5; $i++ ) : ?>
									<img src="<?= ROOT ?>/assets/icons/star-grey.svg" alt="grey star">
								<?php endfor ?>
							</div>
						</div>
						<div class="card__body">
							<div class="card__body__author">
								<p><?= $recipe['profile']['username'] ?></p>
							</div>
							<a href="<?= ROOT ?>/recipes/<?= $recipe['id'] ?>" class="btn btn--invert btn--next">View</a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</section>

		<!-- TODO: Add paginator -->
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>