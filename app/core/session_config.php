<?php

ini_set( 'session.use_only_cookies', 1 );
ini_set( 'session.use_strict_mode', 1 );

session_set_cookie_params( [ 
	'lifetime' => 0,					// Sessions are deleted when a browser is closed
	'path' => '/',            // Accessible across the entire domain
	'domain' => DOMAIN,				// Available to the domain only
	'secure' => true,         // Only sent over HTTPS
	'httponly' => true        // Inaccessible to JavaScript
] );

session_start();

session_regenerate_id( true );

// Generate a token to prevent CSRF attacks
$_SESSION['csrfToken'] ??= bin2hex( random_bytes( 32 ) );

$_SESSION['profile'] =
	! empty( $_SESSION['profile'] )
	? $_SESSION['profile']
	: (
		isset( $_COOKIE['profile'] )
		? json_decode( base64_decode( $_COOKIE['profile'] ), true )
		: null
	);
