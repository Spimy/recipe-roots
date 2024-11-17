<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/recipe-editor.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
	<title>Recipe Roots - <?= $action ?> Recipe</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1><?= $action ?> Recipe</h1>

		<form class="editor" method="post" enctype="multipart/form-data">
			<div class="editor__input">
				<label for="title">Title</label>
				<input type="text" name="title" id="title" required>
			</div>

			<div class="grid">
				<div class="editor__input">
					<label for="thumbnail">Thumbnail</label>
					<div class="input__file">
						<label for="thumbnail">
							<img src="<?= ROOT ?>/assets/icons/image-picker.svg" alt="image picker">
						</label>
						<input type="file" name="thumbnail" id="thumbnail" accept="image/*">
					</div>
				</div>

				<div class="editor--metadata">
					<div class="editor__input">
						<label for="prepTime">Preparation Time (min)</label>
						<input type="text" inputmode="numeric" name="prepTime" id="prepTime">
					</div>

					<div class="editor__input">
						<label for="waitingTime">Waiting Time (min)</label>
						<input type="text" inputmode="numeric" name="waitingTime" id="waitingTime">
					</div>

					<div class="editor__input">
						<label for="servings">Servings</label>
						<input type="text" inputmode="numeric" name="servings" id="servings">
					</div>

					<div class="editor__input">
						<label>Public</label>
						<div class="editor__input--radio">
							<label for="no"><input type="radio" name="public" id="no" value="no" required checked>No</label>
							<label for="yes"><input type="radio" name="public" id="yes" value="yes">Yes</label>
						</div>
					</div>
				</div>
			</div>

			<div class="editor__ingredients">
				<h2>Ingredients</h2>
				<?php $units = [ "tbsp", "tsp", "oz", "fl. oz", "qt", "pt", "gal", "lb", "mL", "kg" ] ?>

				<table role="table" class="editor__ingredients__list">
					<thead role="rowgroup">
						<tr role="row">
							<th role="columnheader">Reorder</th>
							<th role="columnheader">Amount</th>
							<th role="columnheader">Unit</th>
							<th role="columnheader">Ingredient</th>
							<th role="columnheader">Remove</th>
						</tr>
					</thead>

					<tbody role="rowgroup">
						<tr role="row">
							<td role="cell">
								<label draggable="true" ondragend="dragEnd()" ondragover="dragOver(event)"
									ondragstart="dragStart(event)">
									<img src="<?= ROOT ?>/assets/icons/swap.svg" alt="sort">
								</label>
							</td>
							<td role="cell"><input type="text" inputmode="numeric" name="amounts[]" id="amount"></td>
							<td role="cell">
								<select name="units[]" id="unit" class="btn btn--invert">
									<option value="" selected disabled>Select</option>
									<?php foreach ( $units as $unit ) : ?>
										<option value="<?= $unit ?>"><?= $unit ?></option>
									<?php endforeach ?>
								</select>
							</td>
							<td role="cell"><input type="text" name="ingredients[]" id="ingredient"></td>
							<td role="cell">
								<button class="remove-ingredient" type="button">
									<img src="<?= ROOT ?>/assets/icons/close.svg" alt="remove">
								</button>
							</td>
						</tr>
					</tbody>
				</table>

				<button onclick="addIngredient('<?= ROOT ?>')" id="add-ingredient" type="button"
					class="btn btn--invert btn--add">
					Add
				</button>
			</div>

			<div class="editor__input">
				<label for="instructions">Instructions</label>
				<textarea name="instructions" id="instructions" required></textarea>
			</div>

			<button type="submit" class="btn">
				<img src="<?= ROOT ?>/assets/icons/save.svg" alt="save icon">
				Save
			</button>
			<a class="btn btn--error" href="<?= ROOT ?>/recipes">
				Cancel
			</a>
		</form>
	</main>

	<?php include '../app/views/layout/footer.php' ?>

	<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
	<script src="<?= ROOT ?>/assets/js/recipe-editor.js"></script>
</body>