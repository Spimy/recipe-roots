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
		<div class="hero-images">
			<img src="<?= ROOT ?>/assets/images/food2.svg" alt="Top Dish" class="top-dish" />
			<img src="<?= ROOT ?>/assets/images/food1.svg" alt="Bottom Dish" class="bottom-dish" />
		</div>
		<div class="hero-content">
			<h1 class="hero-title">Your Kitchen Assistant</h1>
			<p class="hero-subtitle">
				Create and share your recipes with fellow enthusiasts with easy access
				to all your cooking needs
			</p>
			<button class="hero-button">Get started -></button>
		</div>
	</section>

	<h1 class="features-header">Why Us?</h1>
	<section class="features">
		<div class="feature">
			<div class="feature-images">
				<img src="<?= ROOT ?>/assets/images/Group 235.svg" alt="Phone and laptop" class="view-device" />
			</div>
			<h2>View on any device</h2>
			<p>
				Whether you cook with your phone, tablet, or your laptop nearby, you can use any of them!
			</p>
		</div>
		<div class="feature">
			<div class="feature-images">
				<img src="<?= ROOT ?>/assets/images/rafiki.svg" alt="Group of people talking"
					class="community-driven" />
			</div>
			<h2>Community Driven</h2>
			<p>
				You can find all the recipes you need shared by other fellow cooks!
			</p>
		</div>
		<div class="feature">
			<div class="feature-images">
				<img src="<?= ROOT ?>/assets/images/bro.svg" alt="Girl Shopping online" class="easy-ingredients" />
			</div>
			<h2>Easy Ingredients</h2>
			<p>
				Ran out of ingredients? Check out the latest and freshest produce from your local farmers!
			</p>
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