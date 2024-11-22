<?php

class Home {
	use Controller;

	public function index() {
		if ( isAuthenticated() ) {
			redirect( 'recipes' );
		}

		$this->view( 'home' );
	}
}