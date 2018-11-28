<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The authentication controller.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Auth extends Controller {

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
	public static $path = PHOSTAPP . 'Views/Auth/';

	/**
	 * Register routes for this controller.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function __construct() {

		// Define the controller routes.
		$this->get( 'auth/', array( $this->class, 'missed_login' ) );
		$this->get( 'auth/login/', array( $this->class, 'login' ) );
		$this->get( 'auth/register/', array( $this->class, 'register' ), can_register() );
		$this->get( 'auth/forgot/', array( $this->class, 'forgot' ) );
		$this->post( 'auth/forgot/send/', array( $this->class, 'forgot_send' ) );
		$this->get( 'auth/forgot/reset/:param/', array( $this->class, 'forgot_reset' ) );
		$this->post( 'auth/forgot/update/', array( $this->class, 'forgot_update' ) );
		$this->get( 'auth/logout/', array( $this->class, 'logout' ) );

	}

	/**
	 * Log in to an account.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function login() {

		if ( self::try_login_user() ) {

			return self::redirect( 'dashboard/' );

		} else {

			// Was the form submitted?
			if ( ! empty( $_POST ) ) {

				register_notice( 'login', 'warning', 'Unable to log you in.' );

			}

			return self::view( self::$path . 'login.php', array( 'title' => 'Login' ), true );

		}

	}

	/**
	 * Register a new account.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function register() {

		if ( self::try_register_user() ) {

			return self::redirect( 'dashboard/' );

		} else {

			// Was the form submitted?
			if ( ! empty( $_POST ) ) {

				register_notice( 'register', 'warning', 'Unable to register an account.' );

			}

			return self::view( self::$path . 'register.php', array( 'title' => 'Register' ), true );

		}

	}

	/**
	 * The forgot password form.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function forgot() {

		return self::view( self::$path . 'forgot.php', array( 'title' => 'Forgot Password' ), true );

	}

	/**
	 * Set forgot password token.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function forgot_send() {

		// Did we get a valid email address?
		if ( ! isset( $_POST[ 'email' ] ) || ! filter_var( $_POST[ 'email' ], FILTER_VALIDATE_EMAIL ) ) {

			register_notice( 'forgot_send', 'warning', 'Invalid email submitted.' );

			return self::redirect( 'auth/forgot/' );

		}

		// Setup a new user instance.
		$user = new User;

		// Create a hash of the email.
		$hash = hash_password( $_POST[ 'email' ] );

		/**
		 * Remove unwanted characters and trim to 20.
		 * 
		 * You may be wondering what on Earth is happening right now.
		 * We don't need/want a full hash of the email because all we
		 * need here is a secure and cryptographically random string to
		 * use as an ID for a user to easily reset a password via a URL
		 * parameter we email to them.
		 * 
		 * Please don't panic.
		 */
		$hash = sanitise_text( $hash, '~[^A-Za-z0-9]~' );
		$hash = substr( $hash, 0, 20 );

		/**
		 * Does a user with this email exist?
		 * 
		 * Whilst it doesn't provide a huge amount of
		 * protection, even if the user doesn't exist
		 * let's not make it public and fail silently.
		 */
		if ( $user->fetch( $_POST[ 'email' ], 'email' ) ) {

			// Set the new token and expiry time.
			$user->token_reset = $hash;
			$user->token_expiry = date( 'Y-m-d H:i:s', time() + DAY_IN_SECONDS );

			// Save the token values.
			$user->save();

			// Create the reset URL.
			$reset_url = auth_url( 'forgot/reset/' . $hash . '/' );

			// Create email message.
			$message = "Hello {$user->user_fullname},<br /><br />
Somebody has requested a reset password link for your account.<br /><br />
You don't need to do anything if this wasn't you. If you did want to reset your password, please use the following link:<br /><br />
<a href='" . $reset_url . "'>" . $reset_url . "</a><br /><br />
Thank you,<br />The team at " . blog_name() . ".";

			// Send the email.
			email( $user->user_email, '[' . blog_name() . '] Password Reset', $message, true );

		}

		register_notice( 'forgot_send', 'info', 'Please check your emails.' );

		return self::redirect( 'auth/login/' );

	}

	/**
	 * The password reset form.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function forgot_reset( $params ) {

		// Create a new user instance.
		$user = new User;

		// Get the given user token.
		$token = ( isset( $params[':param'] ) ) ? $params[':param'] : '';

		// Is the token valid?
		if ( ! $user->fetch( $token, 'token_reset' ) ) {

			return self::redirect( 'auth/forgot/' );

		}

		// Has the token expired?
		if ( date( 'Y-m-d H:i:s' ) >= $user->token_expiry ) {

			register_notice( 'forgot_reset', 'warning', 'The token expired.' );

			return self::redirect( 'auth/forgot/' );

		}

		return self::view( self::$path . 'forgot_reset.php', array( 'title' => 'Reset Password', 'user' => $user ), true );

	}

	/**
	 * Update the user password.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function forgot_update() {

		// Do we have a valid token?
		if ( ! isset( $_POST['token'] ) ) {

			return self::redirect( 'auth/forgot/' );

		}

		// Filter the token value.
		$token = create_path( $_POST['token'] );

		// Do we have valid password values?
		if ( ! isset( $_POST['password'] ) || ! isset( $_POST['confirm_password'] ) ) {

			register_notice( 'forgot_update', 'warning', 'Invalid password given.' );

			return self::redirect( 'auth/forgot/reset/' . $token . '/' );

		}

		// Do the new passwords match?
		if ( $_POST['confirm_password'] != trim( $_POST['password'] ) ) {

			register_notice( 'forgot_update', 'warning', 'Passwords did not match.' );

			return self::redirect( 'auth/forgot/reset/' . $token . '/' );

		}

		// Create a new user instance.
		$user = new User;

		// Set the current user up.
		$user->fetch( $token, 'token_reset' );

		/**
		 * Set the new user password.
		 * 
		 * The only filtering we do here is trimming whitespace.
		 * 
		 * NOTE: The 'save()' method inside the User class instance
		 * will automatically hash the password if it detects that
		 * it has changed so we don't need to hash it here.
		 */
		$user->user_password = trim( $_POST['password'] );

		// Reset the token fields.
		$user->token_reset = '';
		$user->token_expiry = '';

		// Save the changes.
		$user->save();

		register_notice( 'forgot_update', 'success', 'Password was reset.' );

		return self::redirect( 'auth/login/' );

	}

	/**
	 * Log the user out.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function logout() {

		// Try and log the user out.
		if ( self::logout_user() ) {

			register_notice( 'logout', 'info', 'You have been logged out.' );

			return self::redirect( 'auth/login/' );

		}

		return self::redirect( '/' );

	}

	/**
	 * Try an log in a user.
	 * 
	 * Call this function in a POST request to attempt to
	 * login a user based on the account credentials given.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean True is successful or false on failure.
	 */
	public static function try_login_user() {

		// Was the request posted?
		if ( is_logged_in() ) {

			return true;

		}

		// Did we get an email & password?
		if ( empty( $_POST ) || ( '' == $_POST['email'] || '' == $_POST['password'] ) ) {

			return false;

		}

		// Create a new user instance.
		$user = new User;

		// Try and find an existing user.
		$result = $user->fetch( $_POST['email'], 'email' );

		// Does the email already exist?
		if ( false === $result ) {

			return false;

		}

		// Is the password incorrect?
		if ( ! password_verify( $_POST['password'], $result['password'] ) ) {

			return false;

		}

		// Update the last login timestamp.
		$user->auth_at = date( 'Y-m-d H:i:s' );

		$user->save();

		// Should we remember the user for longer?
		if ( isset( $_POST['remember'] ) && 'on' == $_POST['remember'] ) {

			$remember = true;

		} else {

			$remember = false;

		}

		return self::login_user( $result['id'], $_POST['email'], $remember );

	}

	/**
	 * Try and register a user.
	 * 
	 * Attempts to register a new user account on
	 * the blog when called.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean True is successful or false on failure.
	 */
	public static function try_register_user() {

		// Bail if registration is turned off.
		if ( ! can_register() ) {

			return false;

		}

		// Bail if the user is already logged in.
		if ( is_logged_in() ) {

			return false;

		}

		// Bail if we didn't get a proper request.
		if ( empty( $_POST ) || ( '' == $_POST['fullname'] || '' == $_POST['email'] || '' == $_POST['password'] ) ) {

			return false;

		}

		// Is this a valid email?
		if ( ! filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ) {

			return false;

		}

		/**
		 * Trim the user password.
		 * 
		 * NOTE: We only want to remove whitespace from the
		 * beginning and end of the password string but nothing
		 * else because we want to allow user's to have as
		 * complex and personalised password as they wish.
		 */
		$_POST['password'] = trim( $_POST['password'] );

		// Create a new user instance.
		$user = new User;

		// Does the email address already exist?
		if ( false !== $user->fetch( $_POST['email'], 'email' ) ) {

			return false;

		}

		// Set the user variables.
		$user->user_fullname = $_POST['fullname'];
		$user->user_email = $_POST['email'];
		$user->user_password = $_POST['password'];

		// Add the new user.
		$create = $user->create();

		// Was the user created?
		if ( false === $create ) {

			return false;

		}

		// Log the user in.
		return self::login_user( $create, $_POST['email'] );

	}

	/**
	 * Set authentication session & cookies.
	 * 
	 * Pass the email and an optional remember me
	 * flag to set the authentication session for the
	 * user trying to login.
	 * 
	 * WARNING! Only use this function once you have
	 * properly authenticated the user by checking their
	 * details as this function does not verify anything.
	 * 
	 * @todo make the authentication work correctly.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string  $id       The ID of the user.
	 * @param string  $email    The user email address.
	 * @param boolean $remember The remember me flag.
	 * 
	 * @return boolean
	 */
	public static function login_user( $id, $email, $remember = false ) {

		// Check the email is valid.
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {

			return false;

		}

		// Create a hash of the email.
		$hash = hash_password( $email );

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT id, email FROM ' . $db->prefix . 'users WHERE email = ? LIMIT 1' );

		// Bind the parameter to the query.
		$query->bindParam( 1, $email );

		// Execute the query.
		$query->execute();

		// Return the values.
		$result = $query->fetch();

		// Is the email address valid?
		if ( ! password_verify( $result['email'], $hash ) ) {

			return false;

		}

		// Set the new user session.
		$_SESSION['id'] = $id;
		$_SESSION['csrf_token'] = self::create_csrf();

		// Should we remember the user for longer?
		if ( true === $remember ) {

			$remember = time() + WEEK_IN_SECONDS;

		} else {

			$remember = time() + DAY_IN_SECONDS;

		}

		// Set the browser cookie.
		setcookie( AUTH_COOKIE, $hash, $remember, '/', blog_domain(), is_secure(), true );

		return true;

	}

	/**
	 * Remove authentication session and cookies.
	 * 
	 * Unsets the current session and deletes all authentication
	 * cookies from the user's browser to log them out permanently.
	 * 
	 * This function will return true if a user has been logged 
	 * out successfully, or will return false if they cannot be
	 * or they were never logged in to begin with.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public static function logout_user() {

		// Is the current user logged in?
		if ( ! is_logged_in() ) {

			return false;

		}

		// Reset the browser cookie.
		setcookie( AUTH_COOKIE, '', time() - DAY_IN_SECONDS, '/', blog_domain(), is_secure(), true );

		// Destroy the current session.
		session_destroy();

		return true;

	}

	/**
	 * Creates a new CSRF token.
	 * 
	 * Creates a random string to act as a CSRF token
	 * for the current logged in user and sets it to the
	 * session automatically and returns the token.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public static function create_csrf() {

		// Create a character set.
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		// Get character length.
		$length = strlen( $chars );

		// Setup the bytes.
		$bytes = '';

		// Create a random byte set.
		for ( $i = 0; $i < 10; $i++ ) {

			$bytes .= $chars[ rand( 0, $length - 1 ) ];

		}

		/**
		 * Is a user logged in?
		 * 
		 * Add or replace the current token with a new one
		 * if the user is logged in to save some time.
		 */
		if ( is_logged_in() ) {

			$_SESSION['csrf_token'] = $bytes;

		}

		return $bytes;

	}

	/**
	 * Get the current CSRF token.
	 * 
	 * Returns the current user's CSRF token or returns
	 * a boolean value of false if they are not logged in.
	 * 
	 * This function will also sanitise the CSRF token to
	 * ensure any illegal characters are removed in case it
	 * has been set with invalid characters.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|string
	 */
	public static function get_csrf() {

		// Is the user logged in?
		if ( ! is_logged_in() ) {

			return false;

		}

		// Is the CSRF even set?
		if ( ! isset( $_SESSION['csrf_token'] ) ) {

			return false;

		}

		return sanitise_text( $_SESSION['csrf_token'], '~[^A-Za-z0-9]~' );

	}

	/**
	 * Verify the CSRF token.
	 * 
	 * Verify the current user's CSRF token to ensure
	 * the action taken is valid at the current time.
	 * 
	 * Even if verification fails, a new CSRF token
	 * will be created and set for the current user.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $csrf The CSRF token to verify.
	 * 
	 * @return boolean
	 */
	public static function verify_csrf( $csrf ) {

		// Check the user is logged in.
		if ( ! is_logged_in() ) {

			return false;

		}

		// Do the CSRFs match?
		if ( $_SESSION['csrf_token'] == $csrf ) {

			$value = true;

		} else {

			$value = false;

		}

		// Regenerate the token.
		self::create_csrf();

		return $value;

	}

}

