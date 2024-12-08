<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/cookbook-editor.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
	<title>Recipe Roots - <?= $action ?> Cookbook</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1><?= $action ?> Cookbook</h1>

		<?php if ( ! empty( $errors ) ) : ?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) : ?>
					<li class="errors__message"><?= escape( $error ) ?></li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>

		<form class="editor" method="post" enctype="multipart/form-data">
			<?php injectCsrfToken(); ?>

			<div class="editor__input">
				<label for="title">Title</label>
				<input type="text" name="title" id="title" value="<?= escape( $data['title'] ?? '' ) ?? null ?>" required>
			</div>

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

				<div class="editor__input">
					<label for="description">Description</label>
					<textarea name="description" id="description"
						required><?= escape( $data['description'] ?? '' ) ?? null ?></textarea>
				</div>
			</div>

			<div class="editor__input">
				<label>Public</label>
				<div class="editor__input--radio">
					<label for="no"><input type="radio" name="public" id="no" value="no" required <?= empty( $data['public'] ) ? 'checked' : ( $data['public'] == 'no' ? 'checked' : '' ) ?>>No</label>
					<label for="yes"><input type="radio" name="public" id="yes" value="yes" <?= empty( $data['public'] ) ? '' : ( $data['public'] == 'yes' ? 'checked' : '' ) ?>>Yes</label>
				</div>
			</div>

			<button type="submit" class="btn">
				<img src="<?= ROOT ?>/assets/icons/save.svg" alt="save icon">
				Save
			</button>
			<a class="btn btn--error"
				href="<?= ROOT ?>/cookbooks/<?= $action == 'Edit' ? escape( $data['id'] ?? '' ) : '' ?>">
				Cancel
			</a>
		</form>
	</main>

	<?php include '../app/views/layout/footer.php' ?>

	<script>
		const thumbnailInput = document.getElementById('thumbnail');

		thumbnailInput.addEventListener('change', (event) => {
			const thumbnail = event.target.files[0];
			if (!thumbnail) return;

			const label = thumbnailInput.parentElement.getElementsByTagName('label')[0];
			if (!label) return;

			const thumbnailPreview = document.createElement('img');
			thumbnailPreview.classList.add('input__file--preview');
			thumbnailPreview.src = URL.createObjectURL(thumbnail);
			label.append(thumbnailPreview);
		});
	</script>
</body>