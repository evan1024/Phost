<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 * 
 * Start the application.
 * 
 * @package Phost
 */

define( 'PHOST', true );
define( 'PHOSTPATH', dirname( __FILE__ ) . '/' );
define( 'PHOSTAPP', PHOSTPATH . 'Phost/' );
define( 'PHOSTCONTENT', PHOSTPATH . 'Content/' );
define( 'PHOSTEXTEND', PHOSTCONTENT . 'Extensions/' );
define( 'PHOSTTHEMES', PHOSTCONTENT . 'Themes/' );
define( 'PHOSTDIR', DIRECTORY_SEPARATOR );
define( 'PHOSTEXT', '.php' );

// Load the app engine.
require_once( PHOSTAPP . 'App.php' );

// Start the application.
$App = new App;
$App->init();

