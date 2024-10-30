<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<title>Recipe Roots - <?= $recipe['title'] ?></title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<h1>Recipe: <?= $recipe['title'] ?></h1>

	<h2>Ingredients</h2>
	<ol>
		<?php foreach ( $recipe['ingredients'] as $ingredient ) : ?>
			<li><?= $ingredient ?></li>
		<?php endforeach ?>
	</ol>

	<h2>Instructions</h2>
	<p><?= $recipe['instructions'] ?></p>

	<h2>Comments</h2>
	<div>
		<?php if ( count( $recipe['comments'] ) > 0 ) : ?>
			<?php foreach ( $recipe['comments'] as $author => $comment ) : ?>
				<div>
					<h3><?= $author ?></h3>
					<h4>Rating:
						<?php for ( $i = 0; $i < min( $comment['rating'], 5 ); $i++ ) : ?>
							★
						<?php endfor ?>
						<?php for ( $i = min( $comment['rating'], 5 ); $i < 5; $i++ ) : ?>
							☆
						<?php endfor ?>
					</h4>
					<p><?= $comment['content'] ?></p>
				</div>
			<?php endforeach ?>
		<?php else : ?>
			<p>No comments yet</p>
		<?php endif ?>
	</div>
</body>

</html>