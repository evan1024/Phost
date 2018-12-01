<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 * 
 * Helper functions for core.
 * 
 * @package Phost
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Define application cookie names.
 * 
 * @since 0.1.0
 */
define( 'TEST_COOKIE', '_phc_test' );
define( 'AUTH_COOKIE', '_phc_auth' );

/**
 * Define time based constants.
 * 
 * @since 0.1.0
 */
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );
define( 'TWO_DAYS_IN_SECONDS', 2 * DAY_IN_SECONDS );
define( 'WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS );
define( 'TWO_WEEKS_IN_SECONDS', 2 * WEEK_IN_SECONDS );
define( 'MONTH_IN_SECONDS', 4 * WEEK_IN_SECONDS );
define( 'SIX_MONTHS_IN_SECONDS', 6 * MONTH_IN_SECONDS );
define( 'YEAR_IN_SECONDS', 12 * MONTH_IN_SECONDS );

/**
 * The application installed flag.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var boolean
 */
$_installed = false;

/**
 * The array of blog settings.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var array
 */
$_settings = array();

/**
 * The home URL default value.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var string
 */
$_home_url = '';

/**
 * The logged in default value.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var boolean
 */
$_logged_in = false;

/**
 * The current user default object.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var mixed
 */
$_current_user = false;

/**
 * The application post types.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var array
 */
$_post_types = array();

/**
 * Current app notices.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var array
 */
$_notices = array();

/**
 * The extension event listeners.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var array
 */
$_event_listeners = array();

/**
 * The currently active theme.
 * 
 * @since 0.1.0
 * 
 * @access private
 * 
 * @var array
 */
$_active_theme = array();

/**
 * The total number of pages (for pagination).
 * 
 * @since 0.1.0
 * 
 * @var int
 */
$_total_pages = 1;

/**
 * Check if the app is installed.
 * 
 * @since 0.1.0
 * 
 * @param boolean $cached Return the cached version.
 * 
 * @return boolean
 */
function is_app_installed( $cached = true ) {

	global $_installed;

	// Are we using the cached value?
	if ( true === $cached ) {

		return $_installed;

	}

	// Do we have a config file?
	if ( ! file_exists( PHOSTPATH . 'config.php' ) ) {

		return false;

	}

	// Do we have valid database constants?
	if (
		! defined( 'DB_HOST' ) ||
		! defined( 'DB_PORT' ) ||
		! defined( 'DB_NAME' ) ||
		! defined( 'DB_USERNAME' ) ||
		! defined( 'DB_PASSWORD' ) ||
		! defined( 'DB_CHARSET' ) ||
		! defined( 'DB_PREFIX' )
	) {

		return false;

	}

	// Create a database instance.
	$db = new Database;

	// Did the connection work?
	if ( ! $db || is_null( $db->connection ) ) {

		return false;

	}

	// Get the total user count.
	$query = $db->connection->query( 'SELECT COUNT(*) FROM ' . $db->prefix . 'users' );
	$result = $query->fetch();

	// Did we get a result?
	if ( ! isset( $result[ 'count(*)' ] ) || '0' == $result[ 'count(*)' ] ) {

		return false;

	}

	// Set the cache value.
	$_installed = true;

	return $_installed;

}

/**
 * Get a blog setting value.
 * 
 * Returns the value of a setting in the form
 * of a string or returns false if an invalid
 * setting has been requested.
 * 
 * Returns the value of a setting saved in the
 * database. All settings values are cached unless
 * the optional cache busting parameter is set to
 * anything but true.
 * 
 * @since 0.1.0
 * 
 * @param string  $setting The settings key to fetch.
 * @param boolean $cached  Return the cached version.
 * 
 * @return mixed
 */
function blog_setting( $key = '', $cached = true ) {

	global $_settings;

	// Has the application been installed?
	if ( ! is_app_installed() ) {

		return false;

	}

	// Bail if we're gettin' nothin'.
	if ( '' == $key ) {

		return false;

	}

	// Return a cached value if we have one.
	if ( true === $cached && isset( $_settings[ $key ] ) ) {

		return $_settings[ $key ];

	}

	// Create a new settings instance.
	$settings = new Setting;
	$settings = $settings->all();

	// Did we get anything?
	if ( empty( $settings ) ) {

		return false;

	}

	// Reset the cache as we're rebuilding anyway.
	$_settings = array();

	// Loop through each and build the cache.
	foreach ( $settings as $setting ) {

		// Add to the cache.
		$_settings[ $setting['setting_key'] ] = $setting['setting_value'];

	}

	// Is the value available now?
	if ( ! isset( $_settings[ $key ] ) ) {

		return false;

	}

	return $_settings[ $key ];

}

/**
 * Returns the site URL.
 * 
 * Provides the blog homepage URL with an optional
 * parameter to append to the end of the URL.
 * 
 * @since 0.1.0
 * 
 * @param string  $path   The additional path to append.
 * @param boolean $cached Return the cached version.
 * 
 * @return string
 */
function home_url( $path = '', $cached = true ) {

	global $_home_url;

	// Should the cached version be returned?
	if ( '' == $_home_url || false === $cached ) {

		// Get the blog domain.
		$domain = blog_domain();

		// Is the URL saved in the database?
		if ( false === filter_var( $domain, FILTER_VALIDATE_URL ) ) {

			/**
			 * Default to the 'server name' if a blog
			 * domain isn't set in the database to use.
			 */
			$url = sanitise_text( $_SERVER['SERVER_NAME'], '~[^A-Za-z-_.]~' );

		} else {

			$url = $domain;

		}

		// Remove the protocol if there is one.
		$url = str_replace( 'http://', '', $url );
		$url = str_replace( 'https://', '', $url );

		// Should we use HTTPS?
		if ( is_secure() ) {

			$protocol = 'https';

		} else {

			$protocol = 'http';

		}

		// Build the URL and cache it.
		$_home_url = $protocol . '://' . $url . '/';

	}

	$url = $_home_url;

	// Check if we have a path.
	if ( '' != $path ) {

		$url = $url . ltrim( $path, '/' );

	}

	return $url;

}

/**
 * Returns the post URL.
 * 
 * @todo implement post URL writing functionality.
 * 
 * @since 0.1.0
 * 
 * @param object $post The object a Post instance.
 * 
 * @return string
 */
function post_url( $post ) {

	// Do we have a valid post instance?
	if ( ! $post instanceof Post ) {

		return false;

	}

	// Create new post type instance.
	$post_type = new PostTypes;

	// Get the actual post type.
	$post_type = $post_type->get( $post->post_type );

	// Did we get a valid post type?
	if ( false === $post_type ) {

		return false;

	}

	// Should the post type be in the URL?
	if ( $post_type[ 'show_in_url' ] ) {

		$path = $post_type[ 'path' ] . '/' . $post->post_path . '/';

	} else {

		$path = $post->post_path . '/';

	}

	return home_url( $path );

}

/**
 * Returns the authentication URL.
 * 
 * @see home_url()
 * 
 * @since 0.1.0
 * 
 * @param string $path The additional path.
 * 
 * @return string
 */
function auth_url( $path = '' ) {

	return home_url( 'auth/' . $path );

}

/**
 * Returns the dashboard URL.
 * 
 * @see home_url()
 * 
 * @since 0.1.0
 * 
 * @param string $path The additional path.
 * 
 * @return string
 */
function dashboard_url( $path = '' ) {

	return home_url( 'dashboard/' . $path );

}

/**
 * Returns the API URL.
 * 
 * @see home_url()
 * 
 * @since 0.1.0
 * 
 * @param string $path The additional path.
 * 
 * @return string
 */
function api_url( $path = '' ) {

	return home_url( 'api/' . $path );

}

/**
 * Return the URL of system assets.
 * 
 * @see home_url()
 * 
 * @since 0.1.0
 * 
 * @param string $path The additional path.
 * 
 * @return string
 */
function assets_url( $path = '' ) {

	return home_url( 'Phost/Assets/' . $path );

}

/**
 * Adds a CSRF token to a URL.
 * 
 * Adds the CSRF token to the user session but
 * won't modify the URL if the user is not logged
 * in as CSRF is unavailable to authenticated users.
 * 
 * @since 0.1.0
 * 
 * @param string $url The URL to modify.
 * 
 * @return string $url
 */
function csrfify_url( $url ) {

	// Bail if not logged in.
	if ( ! is_logged_in() ) {

		return $url;

	}

	// Get the CSRF token.
	$csrf = get_csrf();

	// Break the URL up.
	$parts = parse_url( $url );

	// Do we already have parameters?
	if ( isset( $parts['query'] ) ) {

		$glue = '&';

	} else {

		$glue = '?';

	}

	// Add the token to the URL.
	$url .= $glue . 'csrf_token=' . $csrf;

	return $url;

}

/**
 * Make a new CSRF token.
 * 
 * @since 0.1.0
 * 
 * @return boolean
 */
function create_csrf() {

	return Auth::create_csrf();

}

/**
 * Get the current CSRF token.
 * 
 * @since 0.1.0
 * 
 * @return boolean|string
 */
function get_csrf() {

	return Auth::get_csrf();

}

/**
 * Verify the CSRF token.
 * 
 * Regardless of whether the CSRF token check passed
 * or not, a new CSRF token is created and set for the
 * current user. Authentication is not required.
 * 
 * @since 0.1.0
 * 
 * @param string $csrf The CSRF token to verify.
 * 
 * @return boolean
 */
function verify_csrf( $csrf ) {

	return Auth::verify_csrf( $csrf );

}

/**
 * Return the blog title.
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function blog_name() {

	return blog_setting( 'name' );

}

/**
 * Return the blog domain.
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function blog_domain() {

	return blog_setting( 'domain' );

}

/**
 * Return the blog email.
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function blog_email() {

	return blog_setting( 'email' );

}

/**
 * Return the per page value.
 * 
 * @since 0.1.0
 * 
 * @return int
 */
function blog_per_page() {

	return (int) blog_setting( 'per_page' );

}

/**
 * Return blog language type.
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function blog_lang() {

	return blog_setting( 'language' );

}

/**
 * Checks if public registration is enabled.
 * 
 * @since 0.1.0
 * 
 * @return boolean
 */
function can_register() {

	// Is registration enabled?
	if ( 'on' == blog_setting( 'register' ) ) {

		return true;

	}

	return false;

}

/**
 * Checks if HTTPS connections are enabled.
 * 
 * @since 0.1.0
 * 
 * @return boolean
 */
function is_secure() {

	// Is registration enabled?
	if ( 'on' == blog_setting( 'https' ) ) {

		return true;

	}

	return false;

}

/**
 * Returns the last checked for updates timestamp.
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function update_check() {

	return blog_setting( 'update_check' );

}

/**
 * Returns a boolean value based on whether an update
 * is available to download or not.
 * 
 * @since 0.1.0
 * 
 * @return boolean
 */
function update_available() {

	// Do we have an update?
	if ( '0' != blog_setting( 'update_available' ) ) {

		return true;

	}

	return false;

}

/**
 * Checks if auto update checks are on.
 * 
 * @since 0.1.0
 * 
 * @return boolean
 */
function auto_updates() {

	// Are automatic updates enabled?
	if ( 'on' == blog_setting( 'auto_check' ) ) {

		return true;

	}

	return false;

}

/**
 * Return the current timezone.
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function blog_timezone() {

	return blog_setting( 'timezone' );

}

/**
 * Get the current blog version.
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function blog_version() {

	// Create a new app instance.
	$App = new App;

	return $App->app_version;

}

/**
 * Returns all active extensions.
 * 
 * @since 0.1.0
 * 
 * @return array
 */
function active_extensions() {

	// Get the active extensions setting.
	$active_extensions = blog_setting( 'active_extensions' );

	// Convert to an array and decode it.
	$active_extensions = json_decode( unfilter_text( $active_extensions ), true );

	return $active_extensions;

}

/**
 * Get the requested path.
 * 
 * Returns a sanitised version of the requested
 * path part of the URL.
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function get_path() {

	// Get the requested URL.
	$url = parse_url( $_SERVER['REQUEST_URI'] );

	// Convert to proper HTML entities.
	$url = htmlentities( $url['path'] );

	// Strip the invalid characters.
	$path = sanitise_text( $url, '~[^A-Za-z0-9-_\/]~' );

	// Trim and re-add slashes safely.
	$path = '/' . trim( $path, '/' ) . '/';

	// Did we get a blank path?
	if ( '//' == $path ) {

		$path = '/';

	}

	return $path;

}

/**
 * Return the page title text.
 * 
 * @since 0.1.0
 * 
 * @param string  $title The title to prepend.
 * @param boolean $raw   THe flag to show the raw title or full title.
 * 
 * @return string $title
 */
function load_title( $title = '', $raw = false ) {

	// Force raw output if not installed.
	if ( ! is_app_installed() ) {

		$raw = true;

	}

	// Should we should the raw title only?
	if ( true === $raw ) {

		return $title;

	}

	return $title . ' &mdash; ' . blog_name();

}

/**
 * Fires a callback for event listeners.
 * 
 * @since 0.1.0
 * 
 * @param string $event The name of the event.
 * @param array  $args  The array of event arguments.
 * 
 * @return mixed
 */
function do_event( $event = '', $args = array() ) {

	global $_event_listeners;

	// Do we have any listeners?
	if ( ! isset( $_event_listeners[ $event ] ) ) {

		return false;

	}

	// Do we have any registered events?
	if ( empty( $_event_listeners[ $event ] ) ) {

		return false;

	}

	// Loop through each callback listener.
	foreach ( $_event_listeners[ $event ] as $listener ) {

		// Extract the arguments.
		extract( $args );

		// Perform the callback.
		call_user_func_array( $listener, $args );

	}

	return true;

}

/**
 * Adds a listener to an event.
 * 
 * @since 0.1.0
 * 
 * @param string $event    The name of the event to callback.
 * @param array  $callback The class/function to run as a callback.
 * 
 * @return boolean
 */
function add_listener( $event = '', $callback = array() ) {

	global $_event_listeners;

	// Did we get a valid event name?
	if ( '' == $event ) {

		return false;

	}

	// Can we perform a callback?
	if ( ! is_callable( $callback ) ) {

		return false;

	}

	// Add the event listener.
	$_event_listeners[ $event ][] = $callback;

	return true;

}

/**
 * Get all extensions.
 * 
 * @since 0.1.0
 * 
 * @return boolean|array
 */
function get_all_extensions() {

	// Get all extension directories.
	$dirs = array_diff( scandir( PHOSTEXTEND ), array( '.', '..', '.svn', '.git', '.DS_Store', 'Thumbs.db' ) );

	// Did we get anything?
	if ( empty( $dirs ) ) {

		return false;

	}

	$extensions = array();

	// Loop through each directory as a extension.
	foreach ( $dirs as $dir ) {

		// Does this directory have a JSON file?
		if ( ! file_exists( PHOSTEXTEND . $dir . '/extension.json' ) ) {

			continue;

		}

		// Get the extension JSON details.
		$data = file_get_contents( PHOSTEXTEND . $dir . '/extension.json' );

		// Convert to an array.
		$data = json_decode( $data, true );

		// Does the extension domain match the settings value?
		if ( ! isset( $data[ 0 ][ 'domain' ] ) ) {

			continue;

		}

		// Does the extension already exist?
		if ( isset( $extensions[ $data[ 0 ][ 'domain' ] ] ) ) {

			continue;

		}

		// Save the extension data.
		$extensions[ $data[ 0 ][ 'domain' ] ] = array(
			'name' => isset( $data[ 0 ][ 'name' ] ) ? $data[ 0 ][ 'name' ] : $data[ 0 ][ 'domain' ],
			'description' => isset( $data[ 0 ][ 'description' ] ) ? $data[ 0 ][ 'description' ] : '',
			'domain' => $data[ 0 ][ 'domain' ],
			'function_path' => isset( $data[ 0 ][ 'function_path' ] ) ? $data[ 0 ][ 'function_path' ] : '',
			'version' => isset( $data[ 0 ][ 'version' ] ) ? $data[ 0 ][ 'version' ] : '',
			'author_name' => isset( $data[ 0 ][ 'author_name' ] ) ? $data[ 0 ][ 'author_name' ] : '',
			'author_url' => isset( $data[ 0 ][ 'author_url' ] ) ? $data[ 0 ][ 'author_url' ] : '',
			'licence_name' => isset( $data[ 0 ][ 'licence_name' ] ) ? $data[ 0 ][ 'licence_name' ] : '',
			'licence_url' => isset( $data[ 0 ][ 'licence_url' ] ) ? $data[ 0 ][ 'licence_url' ] : ''
		);

	}

	// Did we get any extensions?
	if ( empty( $extensions ) ) {

		return false;

	}

	return $extensions;

}

/**
 * Check if an extension is installed.
 * 
 * @since 0.1.0
 * 
 * @param string $extension The domain of an extension.
 * 
 * @return boolean
 */
function is_extension_installed( $extension = '' ) {

	// Get all extensions.
	$extensions = get_all_extensions();

	// Does the extension exist?
	if ( ! isset( $extensions[ $extension ] ) ) {

		return false;

	}

	// Get the extension settings data.
	$active_extensions = active_extensions();

	// Do we have any extensions?
	if ( empty( $active_extensions ) ) {

		return false;

	}

	// Is the extension installed?
	if ( in_array( $extension, $active_extensions, true ) ) {

		return true;

	}

	return false;

}

/**
 * Get all installed themes.
 * 
 * @since 0.1.0
 * 
 * @return boolean|array
 */
function get_all_themes() {

	// Get all theme directories.
	$dirs = array_diff( scandir( PHOSTTHEMES ), array( '.', '..', '.svn', '.git', '.DS_Store', 'Thumbs.db' ) );

	// Did we get anything?
	if ( empty( $dirs ) ) {

		return false;

	}

	$themes = array();

	// Loop through each directory as a theme.
	foreach ( $dirs as $dir ) {

		// Does this directory have a JSON file?
		if ( ! file_exists( PHOSTTHEMES . $dir . '/theme.json' ) ) {

			continue;

		}

		// Get the theme JSON details.
		$data = file_get_contents( PHOSTTHEMES . $dir . '/theme.json' );

		// Convert to an array.
		$data = json_decode( $data, true );

		// Does the theme domain match the settings value?
		if ( ! isset( $data[ 0 ][ 'domain' ] ) ) {

			continue;

		}

		// Save the theme data.
		$themes[] = array(
			'name' => isset( $data[ 0 ][ 'name' ] ) ? $data[ 0 ][ 'name' ] : $data[ 0 ][ 'domain' ],
			'description' => isset( $data[ 0 ][ 'description' ] ) ? $data[ 0 ][ 'description' ] : '',
			'domain' => $data[ 0 ][ 'domain' ],
			'template_path' => isset( $data[ 0 ][ 'template_path' ] ) ? $data[ 0 ][ 'template_path' ] : '',
			'version' => isset( $data[ 0 ][ 'version' ] ) ? $data[ 0 ][ 'version' ] : '',
			'author_name' => isset( $data[ 0 ][ 'author_name' ] ) ? $data[ 0 ][ 'author_name' ] : '',
			'author_url' => isset( $data[ 0 ][ 'author_url' ] ) ? $data[ 0 ][ 'author_url' ] : '',
			'licence_name' => isset( $data[ 0 ][ 'licence_name' ] ) ? $data[ 0 ][ 'licence_name' ] : '',
			'licence_url' => isset( $data[ 0 ][ 'licence_url' ] ) ? $data[ 0 ][ 'licence_url' ] : ''
		);

	}

	// Did we get any themes?
	if ( empty( $themes ) ) {

		return false;

	}

	return $themes;

}

/**
 * Return the current theme information.
 * 
 * Returns the information for the current theme in an
 * array format or returns a boolean value of false if
 * an invalid name is specified or the theme isn't found.
 * 
 * @since 0.1.0
 * 
 * @param boolean $cached Return the cached version.
 * 
 * @return boolean|array
 */
function active_theme( $cached = true ) {

	global $_active_theme;

	// Should we use the cached version?
	if ( true === $cached && is_array( $_active_theme ) && ! empty( $_active_theme ) ) {

		return $_active_theme;

	}

	// Get the current theme.
	$theme = blog_setting( 'theme' );

	// Does the theme exist?
	if ( ! file_exists( PHOSTTHEMES . $theme . '/theme.json' ) ) {

		return false;

	}

	// Get the theme JSON details.
	$data = file_get_contents( PHOSTTHEMES . $theme . '/theme.json' );

	// Convert to an array.
	$data = json_decode( $data, true );

	// Does the theme domain match the settings value?
	if ( ! isset( $data[ 0 ][ 'domain' ] ) || $data[ 0 ][ 'domain' ] != $theme ) {

		return false;

	}

	// Save the theme data.
	$_active_theme = array(
		'name' => isset( $data[ 0 ][ 'name' ] ) ? $data[ 0 ][ 'name' ] : $data[ 0 ][ 'domain' ],
		'description' => isset( $data[ 0 ][ 'description' ] ) ? $data[ 0 ][ 'description' ] : '',
		'domain' => $data[ 0 ][ 'domain' ],
		'template_path' => isset( $data[ 0 ][ 'template_path' ] ) ? $data[ 0 ][ 'template_path' ] : '',
		'version' => isset( $data[ 0 ][ 'version' ] ) ? $data[ 0 ][ 'version' ] : '',
		'author_name' => isset( $data[ 0 ][ 'author_name' ] ) ? $data[ 0 ][ 'author_name' ] : '',
		'author_url' => isset( $data[ 0 ][ 'author_url' ] ) ? $data[ 0 ][ 'author_url' ] : '',
		'licence_name' => isset( $data[ 0 ][ 'licence_name' ] ) ? $data[ 0 ][ 'licence_name' ] : '',
		'licence_url' => isset( $data[ 0 ][ 'licence_url' ] ) ? $data[ 0 ][ 'licence_url' ] : ''
	);

	return $_active_theme;

}

/**
 * Returns the active theme domain.
 * 
 * @since 0.1.0
 * 
 * @return boolean|string
 */
function theme_domain() {

	// Get the active theme.
	$theme = active_theme();

	// Does the file path exist?
	if ( ! $theme ) {

		return false;

	}

	return $theme[ 'domain' ];

}

/**
 * Returns the active theme path.
 * 
 * @since 0.1.0
 * 
 * @param string $path The directory or file to append.
 * 
 * @return boolean|string
 */
function theme_path( $path = '' ) {

	// Get the active theme.
	$theme = active_theme();

	// Does the file path exist?
	if ( ! $theme || ! file_exists( PHOSTTHEMES . $theme[ 'domain' ] . '/' . $theme[ 'template_path' ] ) ) {

		return false;

	}

	// Create the path.
	$_path = PHOSTTHEMES . $theme[ 'domain' ] . '/' . $theme[ 'template_path' ];

	// Do we have a path to append?
	if ( '' != $path ) {

		$_path = $_path . '/' . ltrim( $path, '/' );

	}

	return $_path;

}

/**
 * Returns the active theme URL.
 * 
 * @since 0.1.0
 * 
 * @param string $path The directory or file to append.
 * 
 * @return boolean|string
 */
function theme_url( $path = '' ) {

	// Get the active theme.
	$theme = active_theme();

	// Does the file path exist?
	if ( ! $theme ) {

		return false;

	}

	// Create the path.
	$_url = 'Content/Themes/' . $theme[ 'domain' ];

	// Do we have a path to append?
	if ( '' != $path ) {

		$_url = $_url . '/' . ltrim( $path, '/' );

	}

	return home_url( $_url );

}

/**
 * Load the selected template.
 * 
 * Loads the given template via the `view` method
 * within the core application controller.
 * 
 * @since 0.1.0
 * 
 * @param string $path The template to load.
 * 
 * @return mixed
 */
function load_template( $path = '' ) {

	// Bail silently if we get nothing.
	if ( ! is_string( $path ) || '' == $path ) {

		return false;

	}

	return Controller::view( $path );

}

/**
 * Check if the current user is logged in.
 * 
 * This function checks if the current user is logged
 * in or not. Will return true if they are or false if
 * they have not been authenticated.
 * 
 * The value becomes cached after it's first use but
 * setting the optional `cached` parameter to false
 * will refresh the value.
 * 
 * @since 0.1.0
 * 
 * @param boolean $cached Return the cached version.
 * 
 * @return boolean
 */
function is_logged_in( $cached = true ) {

	global $_logged_in;

	// Should we get the cached version?
	if ( $_logged_in && true === $cached ) {

		return $_logged_in;

	}

	// Set the default cached value.
	$_logged_in = false;

	// Do we have an active session and cookie set?
	if ( empty( $_SESSION ) || ! isset( $_SESSION['id'] ) || ! isset( $_COOKIE[ AUTH_COOKIE ] ) ) {

		return $_logged_in;

	}

	// Create new database connection.
	$db = new Database;

	// Bail if the database connection failed.
	if ( ! $db ) {

		return false;

	}

	// Prepare the select statement.
	$query = $db->connection->prepare( 'SELECT email FROM ' . $db->prefix . 'users WHERE ID = ? LIMIT 1' );

	// Bind the parameter to the query.
	$query->bindParam( 1, $_SESSION['id'] );

	// Execute the query.
	$query->execute();

	// Return the values.
	$result = $query->fetch();

	// Did we get a result?
	if ( false === $result ) {

		return $_logged_in;

	}

	// Does the hash match the cookie?
	if ( ! password_verify( $result['email'], $_COOKIE[ AUTH_COOKIE ] ) ) {

		return $_logged_in;

	}

	// Update the cache value.
	$_logged_in = true;

	return $_logged_in;

}

/**
 * Check if a user is an admin.
 * 
 * Check if the user based on the given user id is
 * an admin or not. If a user id is not provided then
 * the currently logged in user will be checked instead.
 * 
 * @since 0.1.0
 * 
 * @param int $user_id The user id to check.
 * 
 * @return boolean
 */
function is_admin( $user_id = 0 ) {

	// Did we get a valid ID?
	if ( 0 === $user_id ) {

		// Set to the current user.
		$user_id = current_user_id();

	}

	// Create a new user instance.
	$user = new User;

	// Fetch the selected user.
	$user->fetch( $user_id );

	// Is this user an admin?
	if ( 'admin' == $user->user_type ) {

		return true;

	}

	return false;

}

/**
 * Check if a user is an author.
 * 
 * Checks if the given user id belongs to a user account
 * that has the type of `author`. This function has a second
 * optional parameter to perform a strict check. If strict is
 * not set, admins will return true for this function.
 * 
 * @since 0.1.0
 * 
 * @param int     $user_id The user id to check.
 * @param boolean $strict  Strictly check the user type.
 * 
 * @return boolean
 */
function is_author( $user_id = 0, $strict = false ) {

	// Did we get a valid ID?
	if ( 0 === $user_id ) {

		// Set to the current user.
		$user_id = current_user_id();

	}

	// Create a new user instance.
	$user = new User;

	// Fetch the selected user.
	$user->fetch( $user_id );

	// Is this user an author (or an admin)?
	if ( ( false === $strict && in_array( $user->user_type, array( 'author', 'admin' ), true ) ) || ( false !== $strict && 'author' == $user->user_type ) ) {

		return true;

	}

	return false;

}

/**
 * Get the current user object.
 * 
 * The value becomes cached after it's first use but
 * setting the optional `cached` parameter to false
 * will refresh the value.
 * 
 * @since 0.1.0
 * 
 * @param boolean $cached Return the cached version.
 * 
 * @return object $user
 */
function current_user( $cached = true ) {

	global $_current_user;

	// Should we get the cached version?
	if ( $_current_user && true === $cached ) {

		return $_current_user;

	}

	// Setup the default user id.
	$user_id = 0;

	// Is the current user logged in?
	if ( is_logged_in() ) {

		$user_id = $_SESSION['id'];

	}

	// Create a new user instance.
	$user = new User;

	// Set the current user up.
	$user->fetch( $user_id );

	// Cache the value for later.
	$_current_user = $user;

	return $_current_user;

}

/**
 * Get the current user id.
 * 
 * @since 0.1.0
 * 
 * @return int
 */
function current_user_id() {

	// Get the current user instance.
	$user = current_user();

	return $user->ID;

}

/**
 * Get the current user id.
 * 
 * This is a helper function for `current_user_id()`.
 * 
 * @since 0.1.0
 * 
 * @see current_user_id()
 * 
 * @return int
 */
function my_id() {

	return current_user_id();

}

/**
 * Is the given user me?
 * 
 * Checks if a given ID is the same one as the
 * current user or not. Returns true if it is
 * and false if not or the user is not logged in.
 * 
 * @since 0.1.0
 * 
 * @param int $user_id The user id to check.
 * 
 * @return boolean
 */
function is_me( $user_id = 0 ) {

	// Is the user logged in?
	if ( ! is_logged_in() ) {

		return false;

	}

	// Get the current user id.
	$current_user_id = current_user_id();

	// Do the IDs match?
	if ( $user_id === $current_user_id ) {

		return true;

	}

	return false;

}

/**
 * Checks if a user can perform an action.
 * 
 * This function checks if the selected user can
 * perform a certain action based on the permissions
 * set on their account.
 * 
 * If the second parameter (which is optional) is left
 * blank, the current user id will be used instead. Returns
 * a boolean value of true if they have permission or false
 * if the user doesn't.
 * 
 * @todo permissions need to be implemented
 *       properly before this can be used.
 * 
 * @since 0.1.0
 * 
 * @param string $action  The action to check permissions for.
 * @param int    $user_id The user id to check against.
 * 
 * @return boolean
 */
function user_can( $action, $user_id = 0 ) {

	return false;

}

/**
 * Sanitise a piece of textual input.
 * 
 * @since 0.1.0
 * 
 * @param string $text      The dirty text that needs cleaning.
 * @param string $regex     The regular expression to use for cleaning.
 * @param string $delimiter The delimiter of the regular expression.
 * 
 * @return string
 */
function sanitise_text( $text = '', $regex = '~[^A-Za-z]~' ) {

	// Make sure we a regular expression.
	if ( '' == $regex || ! is_string( $regex ) ) {

		error( __FUNCTION__, 'Invalid regular expression given for sanitisation.', '0.1.0' );

	}

	return preg_replace( $regex, '', $text );

}

/**
 * Wrapper function to filter URL paths.
 * 
 * @since 0.1.0
 * 
 * @see sanitise_text()
 * 
 * @param string $path The URL path to clean.
 * 
 * @return string
 */
function sanitise_path( $path = '' ) {

	return sanitise_text( $path, '~[^A-Za-z0-9_[-]\/]~' );

}

/**
 * Creates a safe path for posts.
 * 
 * @since 0.1.0
 * 
 * @param string $path The path to filter.
 * 
 * @return string
 */
function create_path( $path ) {

	// Convert to lower-case.
	$path = strtolower( $path );

	// Trim whitespace from the path.
	$path = trim( $path );

	// Strip inner white space.
	$path = str_replace( ' ', '-', $path );

	// Replace multiple hyphens with singular ones.
	$path = preg_replace( '~-+~', '-', $path );

	// Remove any other characters.
	$path = sanitise_text( $path, '~[^A-Za-z0-9-_]~' );

	return $path;

}

/**
 * Filter a string of possible HTML content.
 * 
 * @since 0.1.0
 * 
 * @param string $text The text to filter from HTML.
 * 
 * @return string $text
 */
function filter_text( $text ) {

	return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );

}

/**
 * Reverse the filtering of htmlspecialchars.
 * 
 * @see filter_text()
 * 
 * @since 0.1.0
 * 
 * @param string $text The text to filter into HTML.
 * 
 * @return string $text
 */
function unfilter_text( $text ) {

	return htmlspecialchars_decode( $text, ENT_QUOTES );

}

/**
 * Get posts based on a set of query options.
 * 
 * @uses Query
 * 
 * @since 0.1.0
 * 
 * @param array   $opts      The options for the query.
 * @param boolean $set_pages Flag to set the total pages count.
 * 
 * @return array
 */
function get_posts( $opts = array(), $set_pages = false ) {

	// Force the table name.
	$opts[ 'table' ] = 'posts';

	// Get the posts.
	$items = new Query( $opts );

	$posts = array();

	if ( ! empty( $items->items ) ) {

		if ( false !== $set_pages ) {

			set_total_pages( $items->count );

		}

		// Format each item.
		foreach ( $items->items as $item ) {

			$post = new Post;

			$post->format_array( $item );

			$posts[] = $post;

		}

	}

	return $posts;

}

/**
 * Get menus based on a set of query options.
 * 
 * @uses Query
 * 
 * @since 0.1.0
 * 
 * @param array   $opts      The options for the query.
 * @param boolean $set_pages Flag to set the total pages count.
 * 
 * @return array
 */
function get_menus( $opts = array(), $set_pages = false ) {

	// Force the table name.
	$opts[ 'table' ] = 'menus';

	// Get the menus.
	$items = new Query( $opts );

	$menus = array();

	if ( ! empty( $items->items ) ) {

		if ( false !== $set_pages ) {

			set_total_pages( $items->count );

		}

		// Format each item.
		foreach ( $items->items as $item ) {

			$menu = new Menu;

			$menu->format_array( $item );

			$menus[] = $menu;

		}

	}

	return $menus;

}

/**
 * Get tags based on a set of query options.
 * 
 * @uses Query
 * 
 * @since 0.1.0
 * 
 * @param array   $opts      The options for the query.
 * @param boolean $set_pages Flag to set the total pages count.
 * 
 * @return array
 */
function get_tags( $opts = array(), $set_pages = false ) {

	// Force the table name.
	$opts[ 'table' ] = 'tags';

	// Get the tags.
	$items = new Query( $opts );

	$tags = array();

	if ( ! empty( $items->items ) ) {

		if ( false !== $set_pages ) {

			set_total_pages( $items->count );

		}

		// Format each item.
		foreach ( $items->items as $item ) {

			$tag = new Tag;

			$tag->format_array( $item );

			$tags[] = $tag;

		}

	}

	return $tags;

}

/**
 * Get media based on a set of query options.
 * 
 * @uses Query
 * 
 * @since 0.1.0
 * 
 * @param array   $opts      The options for the query.
 * @param boolean $set_pages Flag to set the total pages count.
 * 
 * @return array
 */
function get_media( $opts = array(), $set_pages = false ) {

	// Force the table name.
	$opts[ 'table' ] = 'media';

	// Get the media.
	$items = new Query( $opts );

	$files = array();

	if ( ! empty( $items->items ) ) {

		if ( false !== $set_pages ) {

			set_total_pages( $items->count );

		}

		// Format each item.
		foreach ( $items->items as $item ) {

			$media = new Media;

			$media->format_array( $item );

			$files[] = $media;

		}

	}

	return $files;

}

/**
 * Get users based on a set of query options.
 * 
 * @uses Query
 * 
 * @since 0.1.0
 * 
 * @param array   $opts      The options for the query.
 * @param boolean $set_pages Flag to set the total pages count.
 * 
 * @return array
 */
function get_users( $opts = array(), $set_pages = false ) {

	// Force the table name.
	$opts[ 'table' ] = 'users';

	// Get the users.
	$items = new Query( $opts );

	$users = array();

	if ( ! empty( $items->items ) ) {

		if ( false !== $set_pages ) {

			set_total_pages( $items->count );

		}

		// Format each item.
		foreach ( $items->items as $item ) {

			$user = new User;

			$user->format_array( $item );

			$users[] = $user;

		}

	}

	return $users;

}

/**
 * Get settings based on a set of query options.
 * 
 * @uses Query
 * 
 * @since 0.1.0
 * 
 * @param array   $opts      The options for the query.
 * @param boolean $set_pages Flag to set the total pages count.
 * 
 * @return array
 */
function get_settings( $opts = array(), $set_pages = false ) {

	// Force the table name.
	$opts[ 'table' ] = 'settings';

	// Get the settings.
	$items = new Query( $opts );

	$settings = array();

	if ( ! empty( $items->items ) ) {

		if ( false !== $set_pages ) {

			set_total_pages( $items->count );

		}

		// Format each item.
		foreach ( $items->items as $item ) {

			$setting = new Setting;

			$setting->format_array( $item );

			$settings[] = $setting;

		}

	}

	return $settings;

}

/**
 * Returns the list of menu items by location.
 * 
 * This function returns an array of menu items based
 * on the given location parameter. If multiple menus
 * have the same location id then the most recently
 * created menu will be used.
 * 
 * @since 0.1.0
 * 
 * @param string $location The menu location ID.
 * 
 * @return array
 */
function get_menu_links( $location = '' ) {

	// Get the menu items.
	$menu = get_menus(
		array(
			'where' => array(
				array(
					'key' => 'location',
					'value' => $location
				)
			),
			'limit' => 1,
			'offset' => 0
		)
	);

	// Did we get anything back?
	if ( empty( $menu ) ) {

		return array();

	}

	return $menu[ 0 ]->menu_list;
	
}

/**
 * Registers a new notice.
 * 
 * @since 0.1.0
 * 
 * @param string  $id         The unique nnotice id.
 * @param string  $type       The type of notice.
 * @param string  $text       The text for this notice.
 * @param boolean $dismiss    The dismiss notice flag.
 * @param boolean $cache_bust The notice cache bust flag.
 * 
 * @return boolean
 */
function register_notice( $id, $type, $text, $dismiss = true, $cache_bust = false ) {

	// Map the notice options.
	$opts = array(
		'id' => $id,
		'type' => $type,
		'text' => $text,
		'dismiss' => $dismiss,
	);

	// Clean up all the options.
	$opts[ 'id' ] = sanitise_text( $opts[ 'id' ], '~[^A-Za-z0-9]~' );
	$opts[ 'type' ] = ( in_array( $opts[ 'type' ], array( 'info', 'success', 'warning' ), true ) ) ? $opts[ 'type' ] : 'info';
	$opts[ 'text' ] = filter_text( $opts[ 'text' ] );
	$opts[ 'dismiss' ] = (boolean) $opts[ 'dismiss' ];

	// Does this notice exist?
	if ( isset( $_SESSION[ 'notices' ] ) && isset( $_SESSION[ 'notices' ][ $opts[ 'id' ] ] ) ) {

		return false;

	}

	// Add the notice to the session.
	$_SESSION[ 'notices' ][ $opts[ 'id' ] ] = $opts;

	// Bust the cache for this notice?
	if ( true === $cache_bust ) {

		update_notice_cache( $opts[ 'id' ] );

	}

	return true;

}

/**
 * Update session set notices.
 * 
 * This function can be used to update the global
 * notices cache if a notice has been added after
 * the application has been initialised but needs
 * outputting before shutdown.
 * 
 * @since 0.1.0
 * 
 * @param string $id The optional id of a notice to update.
 * 
 * @return boolean
 */
function update_notice_cache( $id = '' ) {

	global $_notices;

	// Do we have an id?
	if ( isset( $_SESSION[ 'notices' ] ) && is_array( $_notices ) ) {

		// Do we have an id?
		if ( '' != $id ) {

			// Does the notice exist?
			if ( isset( $_SESSION[ 'notices' ][ $id ] ) ) {

				// Add the specified notice.
				$_notices[ $id ] = $_SESSION[ 'notices' ][ $id ];

				// Remove this notice from the session.
				unset( $_SESSION[ 'notices' ][ $id ] );

				return true;

			}

		} else {

			// Merge the notice arrays together.
			$_notices = array_merge( $_notices, $_SESSION[ 'notices' ] );

			// Clear the session notices.
			$_SESSION[ 'notices' ] = array();

			return true;

		}

	}

	return false;

}

/**
 * Return all notice HTML.
 * 
 * @see register_notice()
 * 
 * @since 0.1.0
 * 
 * @return string
 */
function do_notices() {

	global $_notices;

	// Notices HTML placeholder.
	$output = '';

	// Bail early if we need to.
	if ( empty( $_notices ) || ! is_array( $_notices ) ) {

		return '';

	}

	// Define available notice icons.
	$icons = array(
		'info' => 'info',
		'success' => 'thumbs-up',
		'warning' => 'exclamation-triangle',
	);

	// Loop through each notice and build it.
	foreach ( $_notices as $notice ) {

		// Check we have all required fields.
		if ( ! isset( $notice[ 'id' ] ) || ! isset( $notice[ 'type' ] ) || ! isset( $notice[ 'text' ] ) || ! isset( $notice[ 'dismiss' ] ) ) {

			continue;

		}

		// Can this be dismissed?
		if ( $notice[ 'dismiss' ] ) {

			$dismiss = '<a class="notice__close" role="link" tabindex="0"><i class="fas fa-times"></i></a>';

		} else {

			$dismiss = '';

		}

		$output .= '<div class="notice notice--' . $notice[ 'type' ] . ' ' . $notice[ 'id' ] . '"><div class="notice__icon"><i class="fas fa-' . $icons[ $notice[ 'type' ] ] . '"></i></div><div class="notice__text"><p>' . $notice[ 'text' ] . '</p>' . $dismiss . '</div></div>';

	}

	return $output;

}

/**
 * Parse the Markdown of a posts content.
 * 
 * This function will convert the post content of a given post
 * from Markdown into HTML. It requires a Post object and allows
 * for certain options to be set to alter how the content will
 * be returned from it.
 * 
 * @uses Parsedown
 * 
 * @since 0.1.0
 * 
 * @param object  $post The post object containing post content.
 * @param boolean $opts The options for the Parsedown instance.
 * 
 * @return string
 */
function markify_content( $post, $opts = array() ) {

	// Do we have a post object?
	if ( ! $post instanceof Post ) {

		return '';

	}

	// Setup the Parsedown option defaults.
	$defaults = array(
		'safe_mode' => true,
		'inline_only' => false,
		'line_breaks' => true,
		'escape_html' => false,
		'convert_urls' => true
	);

	// Merge the defaults to the given options.
	$opts = array_merge( $defaults, $opts );

	// Create a new Parsedown object.
	$Parsedown = new Parsedown;

	// Set the Parsedown options.
	$Parsedown->setSafeMode( $opts[ 'safe_mode' ] );
	$Parsedown->setBreaksEnabled( $opts[ 'line_breaks' ] );
	$Parsedown->setMarkupEscaped( $opts[ 'escape_html' ] );
	$Parsedown->setUrlsLinked( $opts[ 'convert_urls' ] );

	// Are we parsing inline only?
	if ( true === $opts[ 'inline_only' ] ) {

		return $Parsedown->line( $post->post_content );

	} else {

		return $Parsedown->text( $post->post_content );

	}

}

/**
 * Return an excerpt of the post content.
 * 
 * @since 0.1.0
 * 
 * @param object $post   The post object containing post content.
 * @param int    $length The length of the excerpt. Default is 140.
 * 
 * @return string $content
 */
function content_excerpt( $post, $length = 140 ) {

	// Do we have a post object?
	if ( ! $post instanceof Post ) {

		return '';

	}

	// Get the post content.
	$content = markify_content( $post );

	// Check the content length.
	if ( strlen( $content ) > $length ) {

		// Strip any HTML.
		$content = strip_tags( $content );

		// Trim it.
		$content = substr( $content, 0, $length );

		// Add the dot, dot, dot.
		$content = $content . "&hellip;";

	}

	return $content;

}

/**
 * Convert password into a secure hash.
 * 
 * Takes the given password in plain text and converts
 * it into a cryptographically secure hash for storing
 * in a database using the `password_hash` function.
 * 
 * Uses the default hashing algorithm (currently Bcrypt)
 * unless the server is running PHP 7.2 or above and has
 * the Argon2 library installed, in which that is used
 * for the algorithm instead.
 * 
 * Returns the hashed password if successfully, otherwise
 * it'll return false if the password failed to be hashed
 * for some reason.
 * 
 * @since 0.1.0
 * 
 * @param string $password The password to hash.
 * @param array  $options  The algorithm options.
 * 
 * @return boolean|string
 */
function hash_password( $password, $options = array() ) {

	// Are we using PHP 7.2 or above?
	if ( version_compare( '7.2', PHP_VERSION, '<=' ) && defined( 'PASSWORD_ARGON2I' ) && 'argon2' == blog_setting( 'flag_pass_hash' ) ) {

		$algo = PASSWORD_ARGON2I;

		// Set default options for Argon2.
		$defaults = array(
			'memory_cost' => 2048,
			'time_cost' => 4,
			'threads' => 4
		);

	} else {

		$algo = PASSWORD_BCRYPT;

		// Set default options for Bcrypt.
		$defaults = array(
			'cost' => 11,
		);

	}

	// Merge the options into the defaults.
	$options = array_merge( $defaults, $options );

	return password_hash( $password, $algo, $options );

}

/**
 * Prepares and uploads a file to the system.
 * 
 * This function is used as a wrapper for the Media API to
 * make it easier for files to be uploaded from form submissions.
 * 
 * @since 0.1.0
 * 
 * @param array $files The $_FILES array from a form.
 * 
 * @return boolean|array
 */
function prepare_file_upload( $files ) {

	// Bail if we don't get a $_FILES array.
	if ( ! is_array( $files ) ) {

		return false;

	}

	// Loop through each file upload.
	foreach ( $files as $upload ) {

		/**
		 * Check if the uploaded file is in a multi file upload
		 * array and if it isn't, force it into one.
		 */
		if ( ! is_array( $upload[ 'name' ] ) ) {

			// Create a temporary uploads array.
			$temp_upload = $upload;

			// Restructure the actual upload array.
			$upload = array(
				'name' => array(
					$temp_upload[ 'name' ]
				),
				'type' => array(
					$temp_upload[ 'type' ]
				),
				'tmp_name' => array(
					$temp_upload[ 'tmp_name' ]
				),
				'error' => array(
					$temp_upload[ 'error' ]
				),
				'size' => array(
					$temp_upload[ 'size' ]
				)
			);

		}

		// Create an index counter.
		$upload_index = 0;

		// Loop through each upload.
		foreach ( $upload[ 'name' ] as $filename ) {

			// Create the file data array.
			$file = array(
				'name' => $filename,
				'type' => $upload[ 'type' ][ $upload_index ],
				'tmp_name' => $upload[ 'tmp_name' ][ $upload_index ],
				'error' => $upload[ 'error' ][ $upload_index ],
				'size' => $upload[ 'size' ][ $upload_index ]
			);

			// Create new media instance.
			$media = new Media;

			// Set the media value.
			$media->_files = $file;

			// Try and upload the image.
			$media->upload();

			// Update the file index.
			$upload_index++;

		}

	}

	return true;

}

/**
 * Send an email from the blog.
 * 
 * @uses PHPMailer
 * 
 * @since 0.1.0
 * 
 * @param string $to      The address to send to.
 * @param string $subject The email subject line.
 * @param string $message The email message.
 * @param mixed  $html    The HTML email flag.
 * 
 * @return mixed
 */
function email( $to, $subject, $message, $html = false ) {

	// Create new PHPMailer instance.
	$mail = new PHPMailer( false );

	// Set the email options.
	$mail->setFrom( blog_email(), blog_name() );
	$mail->addAddress( $to );
	$mail->Subject = $subject;
	$mail->Body = $message;

	// Set default to HTML.
	$mail->isHTML( $html );

	return $mail->send();

}

/**
 * Return the current search query.
 * 
 * Returns the current value in the search query
 * via the GET parameter in a HTML-safe filtered
 * format or returns false if invalid.
 * 
 * @since 0.1.0
 * 
 * @return string|boolean
 */
function get_search_query() {

	// Is there a search query?
	if ( ! isset( $_GET[ 'query' ] ) || '' == $_GET[ 'query' ] ) {

		return false;

	}

	return filter_text( $_GET[ 'query' ] );

}

/**
 * Return the current page number.
 * 
 * @since 0.1.0
 * 
 * @return int $page
 */
function get_page_num() {

	// Is the page parameter set?
	if ( ! isset( $_GET[ 'page' ] ) ) {

		$page = 0;

	} else {

		$page = (int) $_GET[ 'page' ];

	}

	// Are we on page 0?
	if ( 0 === $page ) {

		// Default to 1.
		$page = 1;

	}

	return $page;

}

/**
 * Get the total number of pages.
 * 
 * @since 0.1.0
 * 
 * @return int
 */
function get_total_pages() {

	global $_total_pages;

	return (int) $_total_pages;

}

/**
 * Set the total number of pages.
 * 
 * Sets the total number of pages based on the amount
 * of items given and the value of the items per page.
 * 
 * @since 0.1.0
 * 
 * @param int $items The total number of items returned.
 * 
 * @return int
 */
function set_total_pages( $items = 0 ) {

	global $_total_pages;

	// Force the item count to an int.
	$items = (int) $items;

	// Falback if we get an invalid item count.
	if ( 0 == $items ) {

		$items = 1;

	}

	// Get the total number of pages.
	$_total_pages = ceil( $items / blog_per_page() );

	// Convert from float to int.
	$_total_pages = (int) $_total_pages;

	return $_total_pages;

}

/**
 * Return a link for pagination.
 * 
 * Returns the link for the next and previous buttons
 * based on the current page and the options set.
 * 
 * @since 0.1.0
 * 
 * @param string $url The URL to append the page number to.
 * @param string $dir The direction, accepts either 'next' or 'previous'.
 * 
 * @return boolean|string
 */
function get_pagination_link( $url = '', $dir = 'previous' ) {

	// Which direction are we going in?
	if ( 'previous' != $dir && 'next' != $dir ) {

		return false;

	}

	// Get the current page number offset.
	$page_num = ( 'previous' == $dir ) ? get_page_num() - 1 : get_page_num() + 1;

	// Bail on page one.
	if ( 'previous' == $dir && 1 >= get_page_num() ) {

		return false;

	}

	// Bail on the last page.
	if ( 'next' == $dir && get_total_pages() <= get_page_num() ) {

		return false;

	}

	// Parse the URL into bits.
	$parse = parse_url( $url );

	// Set the query separater.
	$sep = ( isset( $parse[ 'query' ] ) ) ? '&' : '?';

	// Do we already have a query set?
	if ( isset( $parse[ 'query' ] ) ) {

		$sep = '&';

	} else {

		$sep = '?';

	}

	// Built the queried URL.
	return $url . $sep . 'page=' . $page_num;

}

/**
 * Return the current page offset.
 * 
 * @since 0.1.0
 * 
 * @return int $offset
 */
function get_page_offset() {

	// Get the current page number.
	$page = get_page_num();

	// Are we on page 1?
	if ( 1 === $page ) {

		return 0;

	}

	// Get the per page value.
	$per_page = blog_per_page();

	// Create the offset value.
	$offset = ( $per_page * $page ) - $per_page;

	return $offset;

}

/**
 * Always returns true.
 * 
 * @since 0.1.0
 * 
 * @return true
 */
function _return_true() {

	return true;

}

/**
 * Always returns false.
 * 
 * @since 0.1.0
 * 
 * @return false
 */
function _return_false() {

	return false;

}

/**
 * Dump and die a variable.
 * 
 * Do note that this function will call `die`
 * which will stop further script execution.
 * 
 * @since 0.1.0
 * 
 * @param mixed $var The variable to dump.
 * 
 * @return string Var dump of the variable.
 */
function dd( $var ) {

	var_dump( $var );

	if ( ! is_debugging() ) {

		error( __METHOD__, 'Dump and die should not be used without debug mode enabled.', '0.1.0' );

	}

	die();

}

/**
 * Debugging mode handler.
 * 
 * Checks whether the current application
 * state is in debug mode or not before we
 * load everything else.
 * 
 * @since 0.1.0
 * 
 * @return boolean
 */
function is_debugging() {

	// Check for debug mode.
	if ( defined( 'APP_DEBUG' ) && true === APP_DEBUG ) {

		return true;

	}

	return false;

}

/**
 * Returns an error message.
 * 
 * @since 0.1.0
 * 
 * @param        $function The function of method the error occurred in.
 * @param string $message The message of the error that occurred.
 * @param string $version The version of the app since the error was introduced.
 * 
 * @return mixed
 */
function error( $function, $message, $version ) {

	if ( is_debugging() ) {

		if ( defined( 'APP_DEBUG_LOG' ) && true === APP_DEBUG_LOG ) {

			error_log( 'Since version ' . $version . ' in ' . $function . ': ' . $message );

		}

		if ( defined( 'APP_DEBUG_DISPLAY' ) && true === APP_DEBUG_DISPLAY ) {

			trigger_error( 'Since version ' . $version . ' in ' . $function . ': ' . $message );

		}

	}

}