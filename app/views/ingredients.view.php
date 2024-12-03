<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/browse.css">
	<title>Recipe Roots - Ingredients</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header>
			<h1>Buy Ingredients</h1>

			<form class="filter" method="GET">
				<div class="filter__input">
					<input type="text" name="filter" id="filter" value="<?= escape( $_GET['filter'] ?? '' ) ?>">
					<button class="filter__input__submit" type="submit">
						<img src="<?= ROOT ?>/assets/icons/search.svg" alt="search icon">
					</button>
				</div>

				<div class="filter__input">
					<div></div>
					<a class="btn btn--invert btn--error" href="?page=1">Reset</a>
				</div>
			</form>
		</header>

		<section class="grid">
			<?php foreach ( $ingredients as $ingredient ) : ?>
				<article class="card">
					<img class="card__thumbnail" src="<?= escape( $ingredient['thumbnail'] ) ?>"
						alt="<?= extractTitleLetters( escape( $ingredient['ingredient'] ) ) ?>">
					<div>
						<div class="card__head">
							<div class="card__head__title">
								<h2><?= escape( $ingredient['ingredient'] ) ?></h2>
							</div>
							<div class="card__head__info">
								<p><?= escape( $ingredient['price'] ) ?>/<?= escape( $ingredient['unit'] ) ?></p>
							</div>
						</div>
						<div class="card__body">
							<div class="card__body__author">
								<p><?= escape( $ingredient['profile']['username'] ) ?></p>
							</div>

							<!-- TODO: change edit to num input for amount to purchase -->
							<a href="<?= ROOT ?>/dashboard/produce/edit/<?= escape( $ingredient['id'] ) ?>"
								class="btn btn--invert btn--next">
								Edit
							</a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</section>

		<section class="paginator">
			<div>
				<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						page=1">
					«
				</a>
				<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						page=<?= $currentPage == 1 ? $currentPage : $currentPage - 1 ?>">
					←
				</a>
			</div>
			<div>
				<?php foreach ( getPaginatorPages( $currentPage, $totalPages ) as $page ) : ?>
					<a href="
							?
							<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
							page=<?= escape( $page ) ?>" class="btn <?= $page == $currentPage ? 'selected' : '' ?>">
						<?= escape( $page ) ?>
					</a>
				<?php endforeach; ?>
			</div>
			<div>
				<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						page=<?= $currentPage == $totalPages ? $totalPages : $currentPage + 1 ?>">
					→
				</a>
				<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						page=<?= $totalPages ?>">
					»
				</a>
			</div>
		</section>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>