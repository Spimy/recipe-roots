<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<title>Recipe Roots - Invoice</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Invoice</h1>

		<header>
			<h2>Invoice ID: <?= escape( $invoice['invoiceId'] ) ?></h2>
			<p>Date: <?= escape( date( 'd M Y - H:i:s', strtotime( $invoice['createdAt'] ) ) ) ?></p>
		</header>

		<section>
			<?php foreach ( $invoice['purchases'] as $purchase ) : ?>
				<?= escape( $purchase['ingredient']['ingredient'] ) ?> x<?= escape( $purchase['amount'] ) ?> -
				RM<?= number_format( $purchase['ingredient']['price'] * $purchase['amount'], 2 ) ?> from
				<?= escape( $purchase['profile']['username'] ) ?>
				<br>
			<?php endforeach ?>
		</section>

		<p>Total: RM<?= escape( $total ) ?></p>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>