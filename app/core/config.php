<?php

switch ( $_SERVER['SERVER_NAME'] ) {
	case 'localhost':
		/** database config **/
		define( 'DBNAME', 'RecipeRoots' );
		define( 'DBHOST', 'localhost' );
		define( 'DBUSER', 'root' );
		define( 'DBPASS', '' );
		define( 'DBDRIVER', '' );

		define( 'ROOT', 'http://localhost/web2202/assignment/public' );
		break;

	default:
		/** database config **/
		define( 'DBNAME', 'RecipeRoots' );
		define( 'DBHOST', 'localhost' );
		define( 'DBUSER', 'root' );
		define( 'DBPASS', '' );
		define( 'DBDRIVER', '' );

		define( 'ROOT', 'https://recipe-roots.spimy.dev' );
		break;
}

// const works here and is recommended since they are defined at compile time
const APP_NAME = "Recipe Roots";
const APP_DESC = "Your Kitchen Assistant";
const DEBUG = true;
