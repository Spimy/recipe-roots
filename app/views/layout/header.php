<header class="navbar">
	<a href="<?= ROOT ?>/">
		<img class="navbar__logo" src="<?= ROOT ?>/assets/images/logo.svg" alt="Logo" class="logo">
	</a>

	<nav class="navbar__menu">
		<div class="navbar__menu__toggle">
			<input type="checkbox" name="toggle-nav" id="toggle-nav">
			<label for="toggle-nav">☰</label>
		</div>

		<ul role="list" class="navbar__menu__items">
			<li><a href="<?= ROOT ?>/">Home</a></li>

			<?php if ( isset( $_SESSION['profile'] ) ) : ?>
				<?php if ( $_SESSION['profile']['id'] ) : ?>
					<li>
						<button id="recipes-toggle" popovertarget="recipes">Recipes</button>
						<menu class="nav-menu" popover id="recipes" anchor="recipes-toggle">
							<a href="<?= ROOT ?>/recipes">Your Recipes</a>
							<a href="<?= ROOT ?>/recipes/browse">Browse Recipes</a>
							<button popovertarget="recipes">&times;</button>
						</menu>
					</li>
					<li>
						<button id="cookbooks-toggle" popovertarget="cookbooks">Cookbooks</button>
						<menu class="nav-menu" popover id="cookbooks" anchor="cookbooks-toggle">
							<a href="<?= ROOT ?>/cookbooks">Your Cookbooks</a>
							<a href="<?= ROOT ?>/cookbooks/browse">Browse Cookbooks</a>
							<button popovertarget="cookbooks">&times;</button>
						</menu>
					</li>
				<?php endif ?>

				<li><a href="<?= ROOT ?>/settings">Settings</a></li>
				<li><a href="<?= ROOT ?>/signout">Sign Out</a></li>
			<?php else : ?>
				<li><a href="<?= ROOT ?>/signin">Sign In</a></li>
			<?php endif; ?>

			<li class="close-nav"><label for="toggle-nav">&times;</label></li>
		</ul>

	</nav>
</header>