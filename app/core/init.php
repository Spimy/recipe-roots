<?php

spl_autoload_register( function ($classname) {
	require $filename = "../app/models/" . ucfirst( $classname ) . ".model.php";
} );

require 'config.php';
require 'session_config.php';
require 'utils.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'App.php';