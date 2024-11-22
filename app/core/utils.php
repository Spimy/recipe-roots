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

function uploadFile( string $folder, string $tempfile, string $filename ) {
	if ( ! is_dir( '../public/uploads' ) ) {
		mkdir( '../public/uploads' );
	}

	$folderpath = "../public/uploads/$folder";
	if ( ! is_dir( $folderpath ) ) {
		mkdir( $folderpath );
	}

	move_uploaded_file( $tempfile, "$folderpath/$filename" );
	return ROOT . "/uploads/$folder/$filename";
}

function extractTitleLetters( string $title ) {
	$titleWords = explode( ' ', $title );
	$firstLetters = '';

	for ( $i = 0; $i < count( $titleWords ); $i++ ) {
		if ( $i == 2 ) {
			break;
		}
		$firstLetters .= strtoupper( $titleWords[ $i ][0] );
	}

	return $firstLetters;
}

function convertToHoursMins( $mins ) {
	if ( $mins < 1 ) {
		return;
	}

	$hours = floor( $mins / 60 );
	$minutes = $mins % 60;

	$format = $hours > 0 ? '%01d hr %02d min' : '%02d min';
	return $hours > 0 ? sprintf( $format, $hours, $minutes ) : sprintf( $format, $minutes );
}
