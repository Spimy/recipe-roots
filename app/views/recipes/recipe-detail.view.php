<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/recipe-detail.css">
	<title>Recipe Roots - <?= escape( $recipe['title'] ) ?></title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header>
			<div class="heading">
				<h1><?= escape( $recipe['title'] ) ?></h1>
				<div class="heading__dropdown">
					<label for="more"><img src="<?= ROOT ?>/assets/icons/more.svg" alt="more"></label>
					<input type="checkbox" name="more" id="more">

					<!-- TODO: add drop down -->
					<menu class="heading__dropdown__menu">
						<a href="<?= ROOT ?>/recipes/edit/<?= escape( $recipe['id'] ) ?>">Edit</a>
						<button popovertarget="delete-confirm">Delete</button>
					</menu>

					<!-- Pop up for confirm delete -->
					<form popover open role="dialog" id="delete-confirm" method="post" action="<?= ROOT ?>/recipes/delete">
						<?php injectCsrfToken() ?>
						<input type="hidden" name="recipeId" value="<?= escape( $recipe['id'] ) ?>">

						<div>
							<h3>Confirm Delete</h3>
							<p>Are you sure you want to delete <strong><?= escape( $recipe['title'] ) ?></strong>?</p>
						</div>

						<div>
							<button type="button" class="btn btn--invert" popovertarget="delete-confirm">Cancel</button>
							<button class="btn btn--error">Delete</button>
						</div>
					</form>
				</div>
			</div>

			<?php if ( ! empty( $recipeErrors ) ) : ?>
				<ul class="errors">
					<?php foreach ( $recipeErrors as $error ) : ?>
						<li class="errors__message"><?= escape( $error ) ?></li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>

			<div class="metadata">
				<?php if ( $recipe['prepTime'] > 0 ) : ?>
					<div class="metadata__info">
						<img src="<?= ROOT ?>/assets/icons/clock.svg" alt="clock">
						<div>
							<p>Preparation</p>
							<p><?= escape( convertToHoursMins( $recipe['prepTime'] ) ) ?></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $recipe['waitingTime'] > 0 ) : ?>
					<div class="metadata__info">
						<img src="<?= ROOT ?>/assets/icons/watch.svg" alt="watch">
						<div>
							<p>Waiting</p>
							<p><?= escape( convertToHoursMins( $recipe['waitingTime'] ) ) ?></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $recipe['servings'] > 0 ) : ?>
					<div class="metadata__info">
						<img src="<?= ROOT ?>/assets/icons/serving.svg" alt="serving">
						<div>
							<p>Servings</p>
							<p><?= escape( $recipe['servings'] ) ?></p>
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
				<img src="<?= $recipe['thumbnail'] ?>" alt="<?= extractTitleLetters( escape( $recipe['title'] ) ) ?>">
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
								<td role="cell"><?= escape( $ingredient['amount'] ) ?></td>
								<td role="cell"><?= escape( $ingredient['unit'] ) ?></td>
								<td role="cell"><?= escape( $ingredient['ingredient'] ) ?></td>
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
				<h2 id="comments">Comments</h2>

				<?php if ( ! empty( $recipeErrors ) ) : ?>
					<ul class="errors">
						<?php foreach ( $recipeErrors as $error ) : ?>
							<li class="errors__message"><?= escape( $error ) ?></li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>

				<div class="details__comments__container">
					<?php foreach ( $comments as $comment ) : ?>
						<article class="details__comments__comment" id="comment-<?= escape( $comment['id'] ) ?>">
							<div class="details__comments__comment__header">
								<div>
									<img class="avatar" src="<?= escape( $comment['profile']['avatar'] ) ?>"
										alt="<?= extractTitleLetters( escape( $comment['profile']['username'] ) ) ?>">

									<div>
										<p><?= escape( $comment['profile']['username'] ) ?></p>
										<p class="rating">
											<?php for ( $i = 0; $i < min( $comment['rating'], 5 ); $i++ ) : ?>
												<img src="<?= ROOT ?>/assets/icons/star-yellow.svg" alt="rated-star">
											<?php endfor ?>
											<?php for ( $i = min( $comment['rating'], 5 ); $i < 5; $i++ ) : ?>
												<img src="<?= ROOT ?>/assets/icons/star-grey.svg" alt="star">
											<?php endfor ?>
										</p>
									</div>
								</div>
							</div>
							<p><?= escape( $comment['content'] ) ?></p>
						</article>
					<?php endforeach; ?>

					<form class="details__comments__editor" action="<?= ROOT ?>/recipes/comment/add" method="post">
						<?php injectCsrfToken() ?>

						<input type="hidden" name="recipeId" value="<?= escape( $recipe['id'] ) ?>">
						<textarea name="content" id="comment" placeholder="Write a comment..." required></textarea>

						<div class="details__comments__editor__footer">
							<fieldset class="rating-input">
								<input type="radio" value="5" id="stars-star5" name="rating" required>
								<label for="stars-star5" title="5 Stars"></label>
								<input type="radio" value="4" id="stars-star4" name="rating">
								<label for="stars-star4" title="4 Stars"></label>
								<input type="radio" value="3" id="stars-star3" name="rating">
								<label for="stars-star3" title="3 Stars"></label>
								<input type="radio" value="2" id="stars-star2" name="rating">
								<label for="stars-star2" title="2 Stars"></label>
								<input type="radio" value="1" id="stars-star1" name="rating">
								<label for="stars-star1" title="1 Stars"></label>
							</fieldset>

							<button type="submit" class="btn">Comment</button>
						</div>
					</form>
				</div>
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