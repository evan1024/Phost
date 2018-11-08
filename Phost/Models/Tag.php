<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The tag model.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Tag extends Model {

	/**
	 * The tag ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $ID = 0;

	/**
	 * The previous tag ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $previous_ID = 0;

	/**
	 * The tag name.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $tag_name = '';

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
	 * Create a new tag instance.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The tag ID.
	 * 
	 * @return mixed
	 */
	public function __construct( $id = 0 ) {

		if ( 0 !== $id ) {

			$this->fetch( $id );

		}

		// Set the meta type.
		$this->meta_type = 'tags';

	}

	/**
	 * Fetch the selected tag.
	 * 
	 * @since 0.1.0
	 * 
	 * @param mixed  $value The tag value to search for.
	 * @param string $key   The tag key to search for.
	 * 
	 * @return boolean|array
	 */
	public function fetch( $value = 0, $key = 'ID' ) {

		// Bail if we're looking for invalid columns.
		if ( 0 === $value || ! in_array( $key, array( 'ID', 'name' ) ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'tags WHERE ' . $key . ' = ? LIMIT 1' );

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

		// Setup the tag model.
		$this->previous_ID = $this->ID;
		$this->ID = $result['id'];
		$this->tag_name = $result['name'];
		$this->created_at = $result['created_at'];
		$this->updated_at = $result['updated_at'];

		return $result;

	}

	/**
	 * Save the tag instance.
	 * 
	 * Saves the current tag instance regardless
	 * of whether it already exists or not.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function save() {

		// Does the tag already exist?
		if ( false !== $this->fetch( $this->tag_name, 'name' ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the update statement.
		$query = $db->connection->prepare( 'INSERT INTO ' . $db->prefix . 'tags ( name, created_at, updated_at ) VALUES ( ?, ?, ? )' );

		// Set the timestamps.
		$this->created_at = date( 'Y-m-d H:i:s' );
		$this->updated_at = date( 'Y-m-d H:i:s' );

		// Filter the tag name.
		$this->tag_name = create_path( $this->tag_name );

		// Check the tag name isn't blank and still doesn't exist.
		if ( '' == $this->tag_name || $this->exists( $this->tag_name, 'name' ) ) {

			return false;

		}

		// Bind parameters to the query.
		$query->bindParam( 1, $this->tag_name );
		$query->bindParam( 2, $this->created_at );
		$query->bindParam( 3, $this->updated_at );

		// Execute the query.
		$query->execute();

		// Set the new inserted ID.
		$this->ID = $db->connection->lastInsertId();

		return $this->ID;

	}

	/**
	 * Delete an existing tag.
	 * 
	 * Permanently deletes the current tag instance.
	 * Returns true is successful or false on failure.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function delete() {

		// Does the tag already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the delete statement.
		$query = $db->connection->prepare( 'DELETE FROM ' . $db->prefix . 'tags WHERE ID = ? LIMIT 1' );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->ID );

		// Execute the query.
		$query->execute();

		return $this->reset( true );

	}

	/**
	 * Check a tag exists.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The tag ID.
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
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'tags WHERE ID = ? LIMIT 1' );

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
	 * Switches to the previous tag.
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

		// Does the previous tag already exist?
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
		$this->tag_name = '';
		$this->created_at = '';
		$this->updated_at = '';

		return true;

	}

}

