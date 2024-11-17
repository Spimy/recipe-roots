<?php

function show( $output ) {
	echo "<pre>";
	print_r( $output );
	echo "</pre>";
}

function escape( $str ) {
	return htmlspecialchars( $str );
}

function redirect( $path ) {
	header( "Location: " . ROOT . "/" . $path );
	die();
}

function isAuthenticated() {
	return isset( $_SESSION['profile'] );
}

function handleUnauthenticated( string $next ) {
	http_response_code( 401 );
	$_SESSION['require_auth'] = 'You need to be signed in first';
	redirect( "signin?next=$next" );
}