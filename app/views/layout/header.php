<header class="navbar">
	<a href="<?= ROOT ?>/">
		<img class="navbar__logo" src="<?= ROOT ?>/assets/images/logo.svg" alt="Logo" class="logo">
	</a>

	<nav class="navbar__menu">
		<ul role="list" class="navbar__menu__items">
			<li><a href="<?= ROOT ?>/">Home</a></li>
			<li><a href="<?= ROOT ?>/about">About</a></li>

			<?php if ( isset( $_SESSION['profile'] ) ) : ?>
				<li><a href="<?= ROOT ?>/signout">Sign Out</a></li>
			<?php else : ?>
				<li><a href="<?= ROOT ?>/signin">Sign In</a></li>
			<?php endif; ?>
		</ul>
	</nav>
</header>