<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * Loads the application.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
final class App {

	/**
	 * Define the app version.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $app_version = '0.1.0-beta4';

	/**
	 * Define the database version.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $db_version = '0.1.0';

	/**
	 * Minimum PHP version required.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $min_php = '5.6';

	/**
	 * Minimum required MySQL version.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $min_mysql = '5.6';

	/**
	 * The application request model.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @var object
	 */
	private $request;

	/**
	 * Start the application runtime.
	 * 
	 * This should only be run once per
	 * page request to prevent errors.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function init() {

		// Start a new session.
		session_start();

		// Check the current PHP version.
		$this->verify_php_ver();

		// Load required files.
		$this->load_resources();

		// Register core post types.
		$this->register_post_types();

		// Load registered notices.
		$this->load_notices();

		// Create new session request.
		$this->request = new Requests();

		// Check the app is installed.
		$this->check_app_installed();

		// Initialise the theme.
		$this->prepare_theme();

		// Build the page.
		$this->use_controller();

	}

	/**
	 * Verify the server has the minimum PHP version.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @return mixed
	 */
	private function verify_php_ver() {

		// Do we have the correct PHP version?
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {

			die( '<h1>Phost requires a minimum PHP version of 5.6. Please update.</h1>' );

		}

	}

	/**
	 * Load the required system resources.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @return void
	 */
	private function load_resources() {

		// Does the config file exist?
		if ( file_exists( PHOSTPATH . 'config.php' ) ) {

			require_once( PHOSTPATH . 'config.php' );

		}

		require_once( PHOSTAPP . 'Helpers.php' );
		require_once( PHOSTAPP . 'Controllers/Controller.php' );
		require_once( PHOSTAPP . 'Controllers/Front.php' );
		require_once( PHOSTAPP . 'Controllers/Auth.php' );
		require_once( PHOSTAPP . 'Controllers/System.php' );
		require_once( PHOSTAPP . 'Controllers/Dashboard.php' );
		require_once( PHOSTAPP . 'Controllers/API.php' );
		require_once( PHOSTAPP . 'Models/Model.php' );
		require_once( PHOSTAPP . 'Models/Setting.php' );
		require_once( PHOSTAPP . 'Models/Menu.php' );
		require_once( PHOSTAPP . 'Models/User.php' );
		require_once( PHOSTAPP . 'Models/Media.php' );
		require_once( PHOSTAPP . 'Models/Post.php' );
		require_once( PHOSTAPP . 'Models/Tag.php' );
		require_once( PHOSTAPP . 'Database.php' );
		require_once( PHOSTAPP . 'Requests.php' );
		require_once( PHOSTAPP . 'PostTypes.php' );
		require_once( PHOSTAPP . 'Query.php' );
		require_once( PHOSTAPP . 'HTTP.php' );
		require_once( PHOSTAPP . 'Vendor/Parsedown/Parsedown.php' );
		require_once( PHOSTAPP . 'Vendor/PHPMailer/Exception.php' );
		require_once( PHOSTAPP . 'Vendor/PHPMailer/PHPMailer.php' );
		require_once( PHOSTAPP . 'Vendor/PHPMailer/SMTP.php' );
		require_once( PHOSTAPP . 'Vendor/Copydir/Copydir.php' );

	}

	/**
	 * Register core application post types.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @return boolean
	 */
	private function register_post_types() {

		// Create new post type instance.
		$post_types = new PostTypes;

		// Define 'Post' post type.
		$post = array(
			'id' => 'post',
			'path' => 'posts',
			'has_archive' => true,
			'show_in_url' => true,
			'show_in_api' => true,
			'labels' => array(
				'name' => 'Posts',
				'singular' => 'Post',
				'plural' => 'Posts'
			),
			'_is_system' => false
		);

		// Define 'Page' post type.
		$page = array(
			'id' => 'page',
			'path' => 'pages',
			'has_archive' => false,
			'show_in_url' => false,
			'show_in_api' => true,
			'labels' => array(
				'name' => 'Pages',
				'singular' => 'Page',
				'plural' => 'Pages'
			),
			'_is_system' => false
		);

		// Add the new post types.
		$post_types->create( $post );
		$post_types->create( $page );

	}

	/**
	 * Load the session notices.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @return void
	 */
	private function load_notices() {

		global $_notices;

		// Do we even have any notices?
		if ( isset( $_SESSION[ 'notices' ] ) ) {

			// Hand them over.
			$_notices = $_SESSION[ 'notices' ];

			// Reset the session.
			$_SESSION[ 'notices' ] = array();

		}

	}

	/**
	 * Check if the current instance is installed.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @return boolean
	 */
	private function check_app_installed() {

		// Has the app been installed properly?
		if ( ! is_app_installed( false ) ) {

			// Are we hitting the system install endpoint?
			if ( ! $this->request->is_system ) {

				// Set the current domain name.
				$domain = ( isset( $_SERVER[ 'SERVER_NAME' ] ) ) ? $_SERVER[ 'SERVER_NAME' ] : $_SERVER[ 'HTTP_HOST' ];

				// Set the current HTTP.
				$proto = ( isset( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) ) ? $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] : $_SERVER[ 'REQUEST_SCHEME' ];

				/**
				 * Build the final URL that we need to redirect the
				 * user to. I know, this is probably less than ideal
				 * but we're too early in the application initialisation
				 * process so the user needs redirecting forcefully in
				 * a very manual way.
				 */
				$url = $proto . '://' . $domain . '/system/install/';

				// Redirect the user to the installer.
				header( 'Location: ' . $url );

				die();

			}

			return false;

		}

		return true;

	}

	/**
	 * Prepare the selected theme.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @return mixed
	 */
	private function prepare_theme() {

		// Auto load the current theme.
		active_theme( false );

	}

	/**
	 * Pass the request to the relevant controller.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @return mixed
	 */
	private function use_controller() {

		// Find out which controller to use.
		if ( $this->request->is_front ) {

			$Controller = new Front;

		} elseif ( $this->request->is_dashboard ) {

			$Controller = new Dashboard;

		} elseif ( $this->request->is_system ) {

			$Controller = new System;

		} elseif ( $this->request->is_auth ) {

			$Controller = new Auth;

		} elseif ( $this->request->is_api ) {

			$Controller = new API;

		}

		// Load the route.
		$Controller->load_route( $this->request );

	}

	/**
	 * Check for system updates.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public static function check_system_update() {

		// Is ZipArchive installed?
		if ( ! class_exists( 'ZipArchive' ) ) {

			return false;

		}

		// Create a settings instance.
		$settings = new Setting;

		// What update branch are we on?
		if ( 'dev' == blog_setting( 'flag_dev_branch' ) ) {

			// Get the latest release (includes pre-releases like betas).
			$api_url = 'https://api.github.com/repos/danieltj27/Phost/releases';

		} else {

			// Get the latest stable release.
			$api_url = 'https://api.github.com/repos/danieltj27/Phost/releases/latest';

		}

		// Find the latest release.
		$request = new HTTP( $api_url );

		/**
		 * Check the branch again because checking for all
		 * releases returns an array but checking for the latest
		 * only returns a single instance.
		 */
		if ( 'dev' == blog_setting( 'flag_dev_branch' ) ) {

			$latest = ( isset( $request->response[0][ 'tag_name' ] ) ) ? $request->response[0][ 'tag_name' ] : false;

		} else {

			$latest = ( isset( $request->response[ 'tag_name' ] ) ) ? $request->response[ 'tag_name' ] : false;

		}

		// Did we get the latest version number?
		if ( $latest ) {

			// Does the update_available setting exist?
			if ( ! $settings->fetch( 'update_available', 'setting_key' ) ) {

				// No, add the key so we can save it.
				$settings->setting_key = 'update_available';

			}

			// Compare version numbers to determine an update.
			if ( version_compare( $latest, blog_version(), '>' ) ) {

				// An update is available.
				$settings->setting_value = $latest;

			} else {

				// An update is not available.
				$settings->setting_value = '0';

			}

			$settings->save();
			$settings->reset();

			// Does the update_check setting exist?
			if ( ! $settings->fetch( 'update_check', 'setting_key' ) ) {

				// No, add the key so we can save it.
				$settings->setting_key = 'update_check';

			}

			// Update the last checked timestamp.
			$settings->setting_value = date( 'Y-m-d H:i:s' );

			$settings->save();

		} else {

			return false;

		}

		return true;

	}

	/**
	 * Run a system update.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public static function run_system_update() {

		// Is ZipArchive installed?
		if ( ! class_exists( 'ZipArchive' ) ) {

			return false;

		}

		// Do we have any update?
		if ( update_available() ) {

			// Define the update package location.
			$update_package_url = 'https://github.com/danieltj27/Phost/archive/' . blog_setting( 'update_available' ) . '.zip';

			// The path to the local update package.
			$local_update_package_zip = PHOSTPATH . 'PhostUpdatePackage.zip';

			// Create new Zip Archive instance.
			$zip = new ZipArchive();

			// Create a new zip file.
			$zip->open( $local_update_package_zip, ZipArchive::CREATE );

			// Add a faux file.
			$zip->addFromString( 'Phost-' . blog_setting( 'update_available' ) . '_' . date( 'YmdHis' ) . '.txt' , '' );

			// Close the archiver.
			$zip->close();

			// Can we save the update package locally?
			if ( false === file_put_contents( $local_update_package_zip, fopen( $update_package_url, 'r' ) ) ) {

				return false;

			} else {

				// Create a new Zip Archive instance.
				$zip = new ZipArchive;

				// Try and open the update package.
				if ( $zip->open( $local_update_package_zip, ZIPARCHIVE::CREATE ) ) {

					// // Extract and overwrite with the zip contents.
					$zip->extractTo( PHOSTPATH );

					// // Stop the instance.
					$zip->close();

					// Move the update contents to the root.
					$move_upgrade = Copydir( PHOSTPATH . 'Phost-' . blog_setting( 'update_available' ), PHOSTPATH );

					// Remove the temporary update directory.
					unlink( PHOSTPATH . 'Phost-' . blog_setting( 'update_available' ) );

					// Create a settings instance.
					$settings = new Setting;

					// Fetch the update available setting.
					$settings->fetch( 'update_available', 'setting_key' );

					// Reset the value of the setting.
					$settings->setting_value = '0';

					// Save the new value.
					$settings->save();

					// Remove successful update package.
					unlink( $local_update_package_zip );

					return true;

				} else {

					// Remove failed update package.
					unlink( $local_update_package_zip );

					return false;

				}

			}

		}

		return false;

	}

}

