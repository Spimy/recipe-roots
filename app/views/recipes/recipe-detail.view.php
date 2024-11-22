<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/recipe-detail.css">
	<title>Recipe Roots - <?= $recipe['title'] ?></title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header>
			<div class="heading">
				<h1><?= $recipe['title'] ?></h1>
				<div class="heading__dropdown">
					<label for="more"><img src="<?= ROOT ?>/assets/icons/more.svg" alt="more"></label>
					<input type="checkbox" name="more" id="more">

					<!-- TODO: add drop down -->
					<menu class="heading__dropdown__menu">
						<a href="<?= ROOT ?>/recipes/edit/<?= $recipe['id'] ?>">Edit</a>
					</menu>
				</div>
			</div>

			<div class="metadata">
				<?php if ( $recipe['prepTime'] > 0 ) : ?>
					<div class="metadata__info">
						<img src="<?= ROOT ?>/assets/icons/clock.svg" alt="clock">
						<div>
							<p>Preparation</p>
							<p><?= convertToHoursMins( $recipe['prepTime'] ) ?></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $recipe['waitingTime'] > 0 ) : ?>
					<div class="metadata__info">
						<img src="<?= ROOT ?>/assets/icons/watch.svg" alt="watch">
						<div>
							<p>Waiting</p>
							<p><?= convertToHoursMins( $recipe['waitingTime'] ) ?></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $recipe['servings'] > 0 ) : ?>
					<div class="metadata__info">
						<img src="<?= ROOT ?>/assets/icons/serving.svg" alt="serving">
						<div>
							<p>Servings</p>
							<p><?= $recipe['servings'] ?></p>
						</div>
					</div>
				<?php endif; ?>

				<div class="metadata__info">
					<img src="<?= ROOT ?>/assets/icons/globe.svg" alt="globe">
					<div>
						<p>Public</p>
						<p><?= $recipe['public'] ? 'Yes' : 'No' ?></p>
					</div>
				</div>
			</div>
		</header>

		<article class="details">
			<section class="details__thumbnail">
				<img src="<?= $recipe['thumbnail'] ?>" alt="<?= extractTitleLetters( $recipe['title'] ) ?>">
			</section>

			<section class="details__ingredients">
				<h2>Ingredients</h2>
				<table role="table" class="editor__ingredients__list">
					<thead role="rowgroup">
						<tr role="row">
							<th role="columnheader">Amount</th>
							<th role="columnheader">Unit</th>
							<th role="columnheader">Ingredient</th>
						</tr>
					</thead>

					<tbody role="rowgroup">
						<?php foreach ( json_decode( $recipe['ingredients'], true ) as $ingredient ) : ?>
							<tr role="row">
								<td role="cell"><?= $ingredient['amount'] ?></td>
								<td role="cell"><?= $ingredient['unit'] ?></td>
								<td role="cell"><?= $ingredient['ingredient'] ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<ol>
				</ol>
			</section>

			<section id="markdown" class="details__instructions">
				<noscript>
					<p><?= nl2br( escape( $recipe['instructions'] ) ) ?></p>
				</noscript>
			</section>

			<section class="details__comments">
				<h2>Comments</h2>
			</section>
		</article>
	</main>

	<?php include '../app/views/layout/footer.php' ?>

	<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
	<script>
		document.getElementById('markdown').innerHTML = marked.parse(`<?= escape( $recipe['instructions'] ) ?>`);
	</script>
</body>

</html>