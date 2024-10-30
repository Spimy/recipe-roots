<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<title>Recipe Roots</title>
</head>

<body>
	<?php include 'layout/header.php' ?>

	<h1>Home View</h1>

	<ol>
		<?php foreach ( $fruits as $recipe ) : ?>
			<li><?= $recipe ?></li>
		<?php endforeach ?>
	</ol>
</body>

</html>