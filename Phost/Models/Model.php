<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * Core model class.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Model {

	/**
	 * The meta data ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @var int
	 */
	protected $meta_ID = 0;

	/**
	 * The meta data object ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @var int
	 */
	protected $meta_object_ID = 0;

	/**
	 * The meta type.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @var int
	 */
	protected $meta_type = '';

	/**
	 * The meta key.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @var int
	 */
	protected $meta_key = '';

	/**
	 * The meta value.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @var int
	 */
	protected $meta_value = '';

	/**
	 * Fetch a piece of meta data.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @return string|boolean
	 */
	protected function get_meta() {

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'meta WHERE meta_type = ? AND meta_key = ? LIMIT 1' );

		// Bind the parameter to the query.
		$query->bindParam( 1, $this->meta_type );
		$query->bindParam( 2, $this->meta_key );

		// Execute the query.
		$query->execute();

		// Return the values.
		$result = $query->fetch();

		// Did we catch anything?
		if ( false === $result ) {

			return false;

		}

		// Set the meta values.
		$this->meta_ID = $result[ 'id' ];
		$this->meta_object_ID = $result[ 'object_id' ];
		$this->meta_type = $result[ 'meta_type' ];
		$this->meta_key = $result[ 'meta_key' ];
		$this->meta_value = $result[ 'meta_value' ];

	}

	/**
	 * Save a piece of meta data.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @return boolean
	 */
	protected function save_meta() {

		// Bail if we don't have enough data.
		if ( '' == $this->meta_key || '' == $this->meta_type ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'meta WHERE meta_type = ? AND meta_key = ? LIMIT 1' );

		// Bind the parameter to the query.
		$query->bindParam( 1, $this->meta_type );
		$query->bindParam( 2, $this->meta_key );

		// Execute the query.
		$query->execute();

		// Get the result.
		$result = $query->fetch();

		// Does the meta data already exist?
		if ( false !== $result ) {

			// Prepare the update statement.
			$query = $db->connection->prepare( 'UPDATE ' . $db->prefix . 'meta SET object_id = ?, meta_type = ?, meta_key = ?, meta_value = ? WHERE ID = ?' );

			// Bind parameters to the query.
			$query->bindParam( 1, $this->meta_object_ID );
			$query->bindParam( 2, $this->meta_type );
			$query->bindParam( 3, $this->meta_key );
			$query->bindParam( 4, $this->meta_value );
			$query->bindParam( 13, $this->meta_ID );

		} else {

			// Prepare the insert statement.
			$query = $db->connection->prepare( 'INSERT INTO ' . $db->prefix . 'meta ( object_id, meta_type, meta_key, meta_value ) VALUES ( ?, ?, ?, ? )' );

			// Bind parameters to the query.
			$query->bindParam( 1, $this->meta_object_ID );
			$query->bindParam( 2, $this->meta_type );
			$query->bindParam( 3, $this->meta_key );
			$query->bindParam( 4, $this->meta_value );

		}

		// Execute the query.
		$query->execute();

		return true;

	}

	/**
	 * Delete a piece of meta data.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @return boolean
	 */
	protected function delete_meta() {

		// Create new database connection.
		$db = new Database;

		// Prepare the delete statement.
		$query = $db->connection->prepare( 'DELETE FROM ' . $db->prefix . 'meta WHERE meta_type = ? AND meta_key = ? LIMIT 1' );

		// Bind the parameter to the query.
		$query->bindParam( 1, $this->meta_type );
		$query->bindParam( 2, $this->meta_key );

		// Execute the query.
		$query->execute();

		return $this->reset_meta();

	}

	/**
	 * Reset the current meta instance.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	protected function reset_meta() {

		// Reset all meta values.
		$this->meta_ID = 0;
		$this->meta_object_ID = 0;
		$this->meta_type = '';
		$this->meta_key = '';
		$this->meta_value = '';

		return true;

	}

}

