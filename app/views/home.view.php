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

	<section class="hero">
		<h1 class="hero__content__title">Your Kitchen Assistant</h1>
		<p class="hero__content__subtitle">
			Create and share your recipes with fellow enthusiasts with easy access
			to all your cooking needs
		</p>
		<a href="<?= ROOT ?>/signup" class="btn btn--next">Get started</a>
	</section>

	<section class="features">
		<h2>Why Us?</h2>

		<div class="features__items grid">
			<div>
				<img src="<?= ROOT ?>/assets/images/illustrations/devices.svg" alt="Showcasing responsiveness" />
				<div>
					<h3>View on any device</h3>
					<p>
						Whether you cook with your phone, tablet, or your laptop nearby, you can use any of them!
					</p>
				</div>
			</div>
			<div>
				<img src="<?= ROOT ?>/assets/images/illustrations/community.svg" alt="Community driven" />
				<div>
					<h3>Community Driven</h3>
					<p>
						You can find all the recipes you need shared by other fellow cooks!
					</p>
				</div>
			</div>
			<div>
				<img src="<?= ROOT ?>/assets/images/illustrations/purchase.svg" alt="Easily access ingredients" />
				<div>
					<h3>Easy Ingredients</h3>
					<p>
						Ran out of ingredients? Check out the latest and freshest produce from your local farmers!
					</p>
				</div>
			</div>
		</div>
	</section>

	<section class="faq-section">
		<h1>FAQ</h1>

		<div class="faq-item">
			<input type="checkbox" id="faq1" class="faq-checkbox" />
			<label for="faq1" class="faq-question">Are my recipes public?</label>
			<div class="faq-answer">
				<p>
					Your recipes can either be private or public depending on your
					preference.
				</p>
			</div>
		</div>

		<div class="faq-item">
			<input type="checkbox" id="faq2" class="faq-checkbox" />
			<label for="faq2" class="faq-question">Do I need an account for each device?</label>
			<div class="faq-answer">
				<p>
					There is no need for an account for each device. As long as you log
					into your account on a device, you'll only always need one account.
				</p>
			</div>
		</div>

		<div class="faq-item">
			<input type="checkbox" id="faq3" class="faq-checkbox" />
			<label for="faq3" class="faq-question">Can I have both a farmer and normal user account?</label>
			<div class="faq-answer">
				<p>
					Yes, most certainly! If you produce ingredients and also have a
					knack for creating outstanding recipes, then feel free to have access
					to both accounts.
				</p>
			</div>
		</div>

		<div class="faq-item">
			<input type="checkbox" id="faq4" class="faq-checkbox" />
			<label for="faq4" class="faq-question">How long do ingredients take to be delivered?</label>
			<div class="faq-answer">
				<p>
					Your ingredients should be delivered to you within 1-2 hours after
					placing your order.
				</p>
			</div>
		</div>
	</section>

	<?php include 'layout/footer.php' ?>
</body>

</html>