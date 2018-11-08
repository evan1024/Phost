<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The system controller.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class System extends Controller {

	/**
	 * The class name reference.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $class = __CLASS__;

	/**
	 * The controller templates path.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public static $path = PHOSTAPP . 'Views/System/';

	/**
	 * Register routes for this controller.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function __construct() {

		// Define the controller routes.
		$this->get( 'system/', array( $this->class, 'system' ) );
		$this->get( 'system/not-found/', array( $this->class, 'not_found' ) );
		$this->get( 'system/not-authorised/', array( $this->class, 'not_authorised' ) );
		$this->get( 'system/bad-request/', array( $this->class, 'bad_request' ) );
		$this->get( 'system/unknown-error/', array( $this->class, 'unknown_error' ) );
		$this->get( 'system/install/', array( $this->class, 'install_welcome' ) );
		$this->get( 'system/install/setup/', array( $this->class, 'install_setup' ) );
		$this->post( 'system/install/database-setup/', array( $this->class, 'install_database' ) );
		$this->post( 'system/install/account-setup/', array( $this->class, 'install_register' ) );

	}

	/**
	 * Redirect to the unknown error page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function system() {

		return self::redirect('system/unknown-error/');

	}

	/**
	 * The not found error page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function not_found() {

		http_response_code( 404 );

		return self::view( self::$path . 'not_found.php', array( 'title' => 'Not Found &lsaquo; System' ), true );

	}

	/**
	 * The not authorised error page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function not_authorised() {

		http_response_code( 403 );

		return self::view( self::$path . 'not_authorised.php', array( 'title' => 'Not Authorised &lsaquo; System' ), true );

	}

	/**
	 * The bad request error page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function bad_request() {

		http_response_code( 405 );

		return self::view( self::$path . 'bad_request.php', array( 'title' => 'Bad Request &lsaquo; System' ), true );

	}

	/**
	 * The unknown error error page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function unknown_error() {

		http_response_code( 500 );

		return self::view( self::$path . 'unknown_error.php', array( 'title' => 'Unknown Error &lsaquo; System' ), true );

	}

	/**
	 * The install welcome page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function install_welcome() {

		if ( is_app_installed() ) {

			return self::view( self::$path . 'install_finished.php', array( 'title' => 'Install Phost' ), true );

		}

		return self::view( self::$path . 'install_welcome.php', array( 'title' => 'Install Phost' ), true );

	}

	/**
	 * The install setup page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function install_setup() {

		// Redirect away if installed.
		if ( is_app_installed() ) {

			return self::redirect('system/install/');

		}

		// Create a database instance.
		$database = new Database;

		// Can we connect to the database?
		if ( $database->is_connected ) {

			return self::view( self::$path . 'install_register.php', array( 'title' => 'Install Phost' ), true );

		} else {

			return self::view( self::$path . 'install_database.php', array( 'title' => 'Install Phost' ), true );

		}

	}

	/**
	 * The database part of the installation.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function install_database() {

		// Redirect away if installed.
		if ( is_app_installed() ) {

			return self::redirect('system/install/');

		}

		// Did we get any fields?
		if ( ! empty( $_POST ) ) {

			// Loop through each field and sanitise them.
			foreach ( $_POST as $key => $value ) {

				// Don't sanitise a password.
				if ( 'password' == $key ) {

					// Clean it up.
					$_POST[ $key ] = sanitise_text( $value, '~[^A-Za-z0-9.]~' );

					// Remove if empty.
					if ( '' == $_POST[ $key ] || false === $_POST[ $key ] ) {

						unset( $_POST[ $key ] );

					}

				}

			}

		}

		// Did we get all required fields?
		if (
			! isset( $_POST[ 'host' ] ) || '' == $_POST[ 'host' ] ||
			! isset( $_POST[ 'port' ] ) || '' == $_POST[ 'port' ] ||
			! isset( $_POST[ 'name' ] ) || '' == $_POST[ 'name' ] ||
			! isset( $_POST[ 'username' ] ) || '' == $_POST[ 'username' ] ||
			! isset( $_POST[ 'password' ] ) || '' == $_POST[ 'password' ] ||
			! isset( $_POST[ 'prefix' ] ) || '' == $_POST[ 'prefix' ]
		) {

			// Include notice for error.
			register_notice( 'install_setup', 'warning', 'Missing required database information.' );

			return self::redirect('system/install/setup/');

		}

		// Try a new connection.
		try {

			// Create new connection.
			$database = new PDO( "mysql:host=" . $_POST[ 'host' ] . ";port=" . $_POST[ 'port' ] . ";dbname=" . $_POST[ 'name' ], $_POST[ 'username' ], $_POST[ 'password' ] );

			// Set default connection attributes.
			$database->setAttribute( PDO::ATTR_CASE, PDO::CASE_LOWER );
			$database->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$database->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );

		} catch ( PDOException $error ) {

			// Database ain't working.
			register_notice( 'install_setup', 'warning', 'Failed to connect to database.' );

			return self::redirect('system/install/setup/');

		}

		// Create default config contents.
		$config = "<?php

if ( ! defined( 'PHOST' ) ) {

	die();

}

define( 'DB_HOST', '" . $_POST[ 'host' ] . "' );
define( 'DB_PORT', '" . $_POST[ 'port' ] . "' );
define( 'DB_NAME', '" . $_POST[ 'name' ] . "' );
define( 'DB_USERNAME', '" . $_POST[ 'username' ] . "' );
define( 'DB_PASSWORD', '" . $_POST[ 'password' ] . "' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_PREFIX', '" . $_POST[ 'prefix' ] . "' );" . PHP_EOL;

		// Create new config file.
		$file = fopen( PHOSTPATH . 'config.php', 'wb' );

		// Write to the file and close.
		fwrite( $file, $config );
		fclose( $file );

		/**
		 * Create the following tables:
		 * 
		 * - media
		 * - meta
		 * - posts
		 * - settings
		 * - tags
		 * - users
		 */
		$database->query(
			"SET sql_notes = 0;

			CREATE TABLE `" . $_POST[ 'prefix' ] . "menus` (
			  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(250) NOT NULL DEFAULT '',
			  `location` varchar(250) NOT NULL,
			  `list` longtext NOT NULL,
			  `created_at` varchar(250) NOT NULL DEFAULT '',
			  `updated_at` varchar(250) NOT NULL DEFAULT '',
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

			CREATE TABLE IF NOT EXISTS `" . $_POST[ 'prefix' ] . "media` (
			  `ID` int(10) NOT NULL AUTO_INCREMENT,
			  `name` varchar(250) NOT NULL DEFAULT '',
			  `type` varchar(250) NOT NULL DEFAULT '',
			  `dir` varchar(250) NOT NULL DEFAULT '',
			  `sizes` longtext NOT NULL,
			  `author_id` int(10) NOT NULL,
			  `uploaded_at` varchar(250) NOT NULL DEFAULT '',
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

			CREATE TABLE IF NOT EXISTS `" . $_POST[ 'prefix' ] . "meta` (
			  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `object_id` int(10) NOT NULL,
			  `object_type` varchar(250) NOT NULL,
			  `meta_key` varchar(250) NOT NULL DEFAULT '',
			  `meta_value` varchar(250) NOT NULL DEFAULT '',
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

			CREATE TABLE IF NOT EXISTS `" . $_POST[ 'prefix' ] . "posts` (
			  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `title` longtext NOT NULL,
			  `content` longtext NOT NULL,
			  `path` varchar(250) NOT NULL DEFAULT '',
			  `status` varchar(250) NOT NULL DEFAULT '',
			  `type` varchar(250) NOT NULL DEFAULT '',
			  `tags` longtext NOT NULL,
			  `author_id` int(10) NOT NULL,
			  `parent_id` int(10) NOT NULL,
			  `media_id` int(10) NOT NULL,
			  `published_at` varchar(250) NOT NULL DEFAULT '',
			  `created_at` varchar(250) NOT NULL DEFAULT '',
			  `updated_at` varchar(250) NOT NULL DEFAULT '',
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

			CREATE TABLE IF NOT EXISTS `" . $_POST[ 'prefix' ] . "settings` (
			  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `setting_key` varchar(250) NOT NULL DEFAULT '',
			  `setting_value` varchar(250) NOT NULL DEFAULT '',
			  `autoload` varchar(250) NOT NULL DEFAULT '',
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

			CREATE TABLE IF NOT EXISTS `" . $_POST[ 'prefix' ] . "tags` (
			  `ID` int(10) NOT NULL AUTO_INCREMENT,
			  `name` varchar(250) NOT NULL DEFAULT '',
			  `created_at` varchar(250) NOT NULL DEFAULT '',
			  `updated_at` varchar(250) NOT NULL DEFAULT '',
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

			CREATE TABLE IF NOT EXISTS `" . $_POST[ 'prefix' ] . "users` (
			  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `email` varchar(250) NOT NULL DEFAULT '',
			  `password` varchar(250) NOT NULL DEFAULT '',
			  `fullname` varchar(250) NOT NULL DEFAULT '',
			  `type` varchar(250) NOT NULL DEFAULT '',
			  `permissions` longtext NOT NULL,
			  `token_reset` varchar(250) NOT NULL DEFAULT '',
			  `token_expiry` varchar(250) NOT NULL DEFAULT '',
			  `auth_at` varchar(250) NOT NULL DEFAULT '',
			  `created_at` varchar(250) NOT NULL DEFAULT '',
			  `updated_at` varchar(250) NOT NULL DEFAULT '',
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

			SET sql_notes = 1;"
		);

		return self::redirect('system/install/setup/');

	}

	/**
	 * The register part of the installation.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function install_register() {

		// Redirect away if installed.
		if ( is_app_installed() ) {

			return self::redirect('system/install/');

		}

		// Did we get all required fields?
		if (
			! isset( $_POST[ 'name' ] ) || '' == $_POST[ 'name' ] ||
			! isset( $_POST[ 'fullname' ] ) || '' == $_POST[ 'fullname' ] ||
			! isset( $_POST[ 'email' ] ) || '' == $_POST[ 'email' ] ||
			! isset( $_POST[ 'password' ] ) || '' == $_POST[ 'password' ]
		) {

			// Include notice for error.
			register_notice( 'install_setup', 'warning', 'Missing required setup information.' );

			return self::redirect('system/install/setup/');

		}

		// Create a user instance.
		$user = new User;

		// Save/overwrite the new user data.
		$user->user_fullname = $_POST[ 'fullname' ];
		$user->user_email = $_POST[ 'email' ];
		$user->user_password = $_POST[ 'password' ];
		$user->user_type = 'admin';

		if ( ! $user->save() ) {

			// User details weren't right.
			register_notice( 'install_setup', 'warning', 'Invalid account details sent.' );

			return self::redirect('system/install/setup/');

		}

		// Get the protocol in use.
		$proto = ( isset( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) ) ? $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] : $_SERVER[ 'REQUEST_SCHEME' ];

		// Create array of default site settings.
		$values = array(
			'name' => array(
				'setting_key' => 'name',
				'setting_value' => $_POST[ 'name' ]
			),
			'domain' => array(
				'setting_key' => 'domain',
				'setting_value' => $_SERVER[ 'SERVER_NAME' ]
			),
			'theme' => array(
				'setting_key' => 'theme',
				'setting_value' => 'Nude'
			),
			'email' => array(
				'setting_key' => 'email',
				'setting_value' => $_POST[ 'email' ]
			),
			'register' => array(
				'setting_key' => 'register',
				'setting_value' => 'off'
			),
			'updates' => array(
				'setting_key' => 'updates',
				'setting_value' => 'on'
			),
			'language' => array(
				'setting_key' => 'language',
				'setting_value' => 'en_gb'
			),
			'timezone' => array(
				'setting_key' => 'timezone',
				'setting_value' => 'Europe/London'
			),
			'https' => array(
				'setting_key' => 'https',
				'setting_value' => ( 'https' == $proto ) ? 'on' : 'off'
			),
			'hsts' => array(
				'setting_key' => 'hsts',
				'setting_value' => 'off'
			),
			'debug' => array(
				'setting_key' => 'debug',
				'setting_value' => 'off'
			),
			'update_check' => array(
				'setting_key' => 'update_check',
				'setting_value' => date( 'Y-m-d H:i:s' )
			),
			'update_available' => array(
				'setting_key' => 'update_available',
				'setting_value' => '0'
			),
			'per_page' => array(
				'setting_key' => 'per_page',
				'setting_value' => 10
			)
		);

		// Create a setting instance.
		$setting = new Setting;

		// Loop through and add each setting.
		foreach ( $values as $value ) {

			// Set the values.
			$setting->setting_key = $value[ 'setting_key' ];
			$setting->setting_value = $value[ 'setting_value' ];

			// Save and reset.
			$setting->save();
			$setting->reset();

		}

		return self::redirect('system/install/');

	}

}

