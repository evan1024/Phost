<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The user model.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class User extends Model {

	/**
	 * The user ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $ID = 0;

	/**
	 * The previous user ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $previous_ID = 0;

	/**
	 * The user's email address.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $user_email = '';

	/**
	 * The user's password.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $user_password = '';

	/**
	 * The user's full name.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $user_fullname = '';

	/**
	 * The user's admin status.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $user_type = '';

	/**
	 * The user's permissions.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $user_permissions = '';

	/**
	 * The notify flag for a user.
	 * 
	 * This value is used to determine if a user
	 * should be notified of changes to their account
	 * but is not saved in the database as a reference.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $user_notify = false;

	/**
	 * The user's password reset token.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $token_reset = '';

	/**
	 * The user's reset token expiry.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $token_expiry = '';

	/**
	 * The last authenticated at timestamp.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $auth_at = '';

	/**
	 * The created at timestamp.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $created_at = '';

	/**
	 * The updated at timestamp.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $updated_at = '';

	/**
	 * Create a new user instance.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The user's ID.
	 * 
	 * @return mixed
	 */
	public function __construct( $id = 0 ) {

		if ( 0 !== $id ) {

			$this->fetch( $id );

		}

		// Set the meta type.
		$this->meta_type = 'users';

	}

	/**
	 * Fetch the selected user.
	 * 
	 * Fetches a user either by the user ID, username
	 * or email address. Defaults to the user ID.
	 * 
	 * @todo allowing fetch by any default column name.
	 * 
	 * @since 0.1.0
	 * 
	 * @param mixed  $value The user value to search for.
	 * @param string $key   The user key to search for.
	 * 
	 * @return boolean|array
	 */
	public function fetch( $value = 0, $key = 'ID' ) {

		// Bail if we're looking for invalid columns.
		if ( 0 === $value || ! in_array( $key, array( 'ID', 'email', 'token_reset' ) ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'users WHERE ' . $key . ' = ? LIMIT 1' );

		// Bind the parameter to the query.
		$query->bindParam( 1, $value );

		// Execute the query.
		$query->execute();

		// Return the values.
		$result = $query->fetch();

		// We didn't find anyone, right?
		if ( false === $result ) {

			return false;

		}

		// Setup the user model.
		$this->previous_ID = $this->ID;
		$this->ID = $result['id'];
		$this->user_email = $result['email'];
		$this->user_password = $result['password'];
		$this->user_fullname = $result['fullname'];
		$this->user_type = $result['type'];
		$this->user_permissions = $result['permissions'];
		$this->token_reset = $result['token_reset'];
		$this->token_expiry = $result['token_expiry'];
		$this->auth_at = $result['auth_at'];
		$this->created_at = $result['created_at'];
		$this->updated_at = $result['updated_at'];

		return $result;

	}

	/**
	 * Create a new user.
	 * 
	 * Returns the ID of the newly created user or
	 * returns a boolean value of false if it failed.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function create() {

		// Does the user already exist?
		if ( false !== $this->exists( $this->ID ) ) {

			return false;

		}

		// Is this email address valid?
		if ( ! filter_var( $this->user_email, FILTER_VALIDATE_EMAIL ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the insert statement.
		$query = $db->connection->prepare( 'INSERT INTO ' . $db->prefix . 'users ( email, password, fullname, type, permissions, token_reset, token_expiry, auth_at, created_at, updated_at ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )' );

		// Set the timestamps.
		$this->auth_at = date( 'Y-m-d H:i:s' );
		$this->created_at = date( 'Y-m-d H:i:s' );
		$this->updated_at = date( 'Y-m-d H:i:s' );

		// Hash the password now so it's not plain-text for the rest of the instance.
		$this->user_password = hash_password( $this->user_password );

		// Filter and trim names and usernames.
		$this->user_fullname = filter_text( trim( $this->user_fullname ) );

		// Is this user an admin?
		if ( 'admin' != $this->user_type ) {

			$this->user_type = 'user';

		}

		/**
		 * @todo Implement permissions.
		 */
		$this->user_permissions = '';

		// Bind parameters to the query.
		$query->bindParam( 1, $this->user_email );
		$query->bindParam( 2, $this->user_password );
		$query->bindParam( 3, $this->user_fullname );
		$query->bindParam( 4, $this->user_type );
		$query->bindParam( 5, $this->user_permissions );
		$query->bindParam( 6, $this->token_reset );
		$query->bindParam( 7, $this->token_expiry );
		$query->bindParam( 8, $this->auth_at );
		$query->bindParam( 9, $this->created_at );
		$query->bindParam( 10, $this->updated_at );

		// Execute the query.
		$query->execute();

		// Set the new inserted ID.
		$this->ID = $db->connection->lastInsertId();

		return $this->ID;

	}

	/**
	 * Save the user instance.
	 * 
	 * Saves the current user instance to the database
	 * or creates a new user if the ID doesn't exist.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function save() {

		// Does the user already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return $this->create();

		}

		// Create new database connection.
		$db = new Database;

		// Set the updated timestamp.
		$this->updated_at = date( 'Y-m-d H:i:s' );

		// Filter and trim names and usernames.
		$this->user_fullname = filter_text( trim( $this->user_fullname ) );

		// Is this user an admin?
		if ( 'admin' != $this->user_type ) {

			$this->user_type = 'user';

		}

		// Prepare password fetch statement.
		$query = $db->connection->prepare( 'SELECT id, email, password FROM ' . $db->prefix . 'users WHERE ID = ? LIMIT 1' );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->ID );

		// Execute the query.
		$query->execute();

		// Return the values.
		$result = $query->fetch();

		// Has the password changed?
		if ( '' == $this->user_password || $this->user_password == $result['password'] ) {

			// Set to the current password (unchanged).
			$this->user_password = $result['password'];

		} else {

			// Consider it new and re-hash it.
			$this->user_password = hash_password( $this->user_password );

		}

		// Prepare the update statement.
		$query = $db->connection->prepare( 'UPDATE ' . $db->prefix . 'users SET email = ?, password = ?, fullname = ?, type = ?, permissions = ?, token_reset = ?, token_expiry = ?, auth_at = ?, created_at = ?, updated_at = ? WHERE ID = ?' );

		/**
		 * @todo Implement permissions.
		 */
		$this->user_permissions = '';

		// Bind parameters to the query.
		$query->bindParam( 1, $this->user_email );
		$query->bindParam( 2, $this->user_password );
		$query->bindParam( 3, $this->user_fullname );
		$query->bindParam( 4, $this->user_type );
		$query->bindParam( 5, $this->user_permissions );
		$query->bindParam( 6, $this->token_reset );
		$query->bindParam( 7, $this->token_expiry );
		$query->bindParam( 8, $this->auth_at );
		$query->bindParam( 9, $this->created_at );
		$query->bindParam( 10, $this->updated_at );
		$query->bindParam( 11, $this->ID );

		// Execute the query.
		$query->execute();

		return $this->ID;

	}

	/**
	 * Delete an existing user.
	 * 
	 * Permanently deletes the current user instance.
	 * Returns true is successful or false on failure.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function delete() {

		// Does the user already exist?
		if ( false === $this->fetch( $this->ID ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the delete statement.
		$query = $db->connection->prepare( 'DELETE FROM ' . $db->prefix . 'users WHERE ID = ? LIMIT 1' );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->ID );

		// Execute the query.
		$query->execute();

		return $this->reset( true );

	}

	/**
	 * Format an array of user data into an object.
	 * 
	 * This function is for formatting arrays of data that have
	 * been fetched directly from the database and transforming
	 * it into a proper User object.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $data The array of user data to format.
	 * 
	 * @return void
	 */
	public function format_array( $data = array() ) {

		// Do we have anything in this array?
		if ( is_array( $data ) && ! empty( $data ) ) {

			$this->previous_ID = $this->ID;
			$this->ID = isset( $data[ 'id' ] ) ? $data[ 'id' ] : 0;
			$this->user_email = isset( $data['email'] ) ? $data[ 'email' ] : '';
			$this->user_password = isset( $data['password'] ) ? $data[ 'password' ] : '';
			$this->user_fullname = isset( $data['fullname'] ) ? $data[ 'fullname' ] : '';
			$this->user_type = isset( $data['type'] ) ? $data[ 'type' ] : '';
			$this->user_permissions = isset( $data['permissions'] ) ? $data[ 'permissions' ] : '';
			$this->token_reset = isset( $data['token_reset'] ) ? $data[ 'token_reset' ] : '';
			$this->token_expiry = isset( $data['token_expiry'] ) ? $data[ 'token_expiry' ] : '';
			$this->auth_at = isset( $data['auth_at'] ) ? $data[ 'auth_at' ] : '';
			$this->created_at = isset( $data['created_at'] ) ? $data[ 'created_at' ] : '';
			$this->updated_at = isset( $data['updated_at'] ) ? $data[ 'updated_at' ] : '';

		}

	}

	/**
	 * Check a user exists.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The user ID.
	 * 
	 * @return boolean
	 */
	public function exists( $id = 0 ) {

		// Bail with an invalid id.
		if ( 0 == $id ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'users WHERE ID = ? LIMIT 1' );

		// Bind the id to the query.
		$query->bindParam( 1, $id );

		// Execute the query.
		$query->execute();

		// Return the values.
		$result = $query->fetch();

		if ( false === $result ) {

			return false;

		}

		return true;

	}

	/**
	 * Switches to the previous user.
	 * 
	 * Resets the current instance to the previous one
	 * based on the ID saved. If the previous ID is invalid
	 * the current instance will be reset to the defaults.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function previous() {

		// Does the previous object exist?
		if ( false === $this->fetch( $this->previous_ID ) ) {

			$this->reset( true );

			return false;

		}

		// Go back to the previous to object.
		$this->fetch( $this->previous_ID );

		return true;

	}

	/**
	 * Reset the current instance.
	 * 
	 * Resets the current instance to the default
	 * object values within this model. Accepts a
	 * parameter to retain the previous ID history.
	 * 
	 * @since 0.1.0
	 * 
	 * @param boolean $history Flag to keep previous ID history.
	 * 
	 * @return boolean
	 */
	public function reset( $history = false ) {

		// Should we keep the previous ID?
		if ( true === $history ) {

			$this->previous_ID = $this->ID;

		} else {

			$this->previous_ID = 0;

		}

		// Reset the objects.
		$this->ID = 0;
		$this->user_email = '';
		$this->user_password = '';
		$this->user_fullname = '';
		$this->user_type = '';
		$this->user_permissions = '';
		$this->auth_at = '';
		$this->created_at = '';
		$this->updated_at = '';

		return true;

	}

}

