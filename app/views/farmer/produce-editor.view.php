<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/produce-editor.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
	<title>Recipe Roots - <?= $action ?> Produce</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1><?= $action ?> Produce</h1>

		<?php if ( ! empty( $errors ) ) : ?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) : ?>
					<li class="errors__message"><?= escape( $error ) ?></li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>

		<form class="editor" method="post" enctype="multipart/form-data">
			<?php injectCsrfToken(); ?>

			<div class="grid">
				<div class="editor__input">
					<label for="thumbnail">Thumbnail</label>
					<div class="input__file">
						<label for="thumbnail">
							<img src="<?= ROOT ?>/assets/icons/image-picker.svg" alt="image picker">

							<?php if ( isset( $data['thumbnail'] ) ) : ?>
								<img src="<?= escape( $data['thumbnail'] ?? '' ) ?>" alt="" class="input__file--preview">
								<input type="text" name="thumbnail" value="<?= escape( $data['thumbnail'] ?? '' ) ?>" hidden>
							<?php endif; ?>

							<noscript>
								<p>Image preview does not work without JavaScript.</p>
								<p>Refer to file name below instead.</p>
							</noscript>
						</label>
						<input type="file" name="thumbnail" id="thumbnail" accept=".png, .gif, .jpeg, .jpg">
					</div>
				</div>

				<div class="editor--metadata">
					<div class="editor__input">
						<label for="ingredient">Ingredient</label>
						<input type="text" name="ingredient" id="ingredient"
							value="<?= escape( $data['ingredient'] ?? '' ) ?? null ?>" required>
					</div>

					<div class="editor__column">
						<div class="editor__input">
							<label for="price">Price</label>
							<input type="text" inputmode="numeric" name="price" id="price"
								value="<?= escape( $data['price'] ?? '' ) ?? null ?>" required>
						</div>

						<span>/</span>

						<div class="editor__input">
							<label for="unit">Unit</label>
							<div class="filter__input">
								<select class="btn btn--invert" name="unit" id="unit" required>
									<?php foreach ( INGREDIENT_UNITS as $unit ) : ?>
										<option value="<?= $unit ?>"><?= $unit ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<button type="submit" class="btn">
						<img src="<?= ROOT ?>/assets/icons/save.svg" alt="save icon">
						Save
					</button>
					<a class="btn btn--error"
						href="<?= ROOT ?>/dashboard/<?= $action == 'Edit' ? escape( $data['id'] ?? '' ) : '' ?>">
						Cancel
					</a>
				</div>
			</div>
		</form>
	</main>

	<?php include '../app/views/layout/footer.php' ?>

	<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
	<script src="<?= ROOT ?>/assets/js/recipe-editor.js"></script>
	<script src="<?= ROOT ?>/assets/js/drag-drop-touch.js"></script>
</body>