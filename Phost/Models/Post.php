<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The post model.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Post extends Model {

	/**
	 * The post ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $ID = 0;

	/**
	 * The previous post ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $previous_ID = 0;

	/**
	 * The post title.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $post_title = '';

	/**
	 * The post content.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $post_content = '';

	/**
	 * The post path.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $post_path = '';

	/**
	 * The post status.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $post_status = '';

	/**
	 * The post type.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $post_type = '';

	/**
	 * The post tags.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $post_tags = array();

	/**
	 * The post author.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $post_author_ID = 0;

	/**
	 * The post parent ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $post_parent_ID = 0;

	/**
	 * The featured post image.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $post_media_ID = 0;

	/**
	 * The published at timestamp.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $published_at = '';

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
	 * Create a new post instance.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The post ID.
	 * 
	 * @return mixed
	 */
	public function __construct( $id = 0 ) {

		if ( 0 !== $id ) {

			$this->fetch( $id );

		}

		// Set the meta type.
		$this->meta_type = 'posts';

	}

	/**
	 * Fetch the selected post.
	 * 
	 * @since 0.1.0
	 * 
	 * @param mixed  $value The post value to search for.
	 * @param string $key   The post key to search for.
	 * 
	 * @return boolean|array
	 */
	public function fetch( $value = 0, $key = 'ID' ) {

		// Bail if we're looking for invalid columns.
		if ( 0 === $value || ! in_array( $key, array( 'ID', 'title', 'path' ) ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'posts WHERE ' . $key . ' = ? LIMIT 1' );

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

		// Setup the post model.
		$this->previous_ID = $this->ID;
		$this->ID = $result['id'];
		$this->post_title = $result['title'];
		$this->post_content = $result['content'];
		$this->post_path = $result['path'];
		$this->post_status = $result['status'];
		$this->post_tags = json_decode( $result['tags'], true );
		$this->post_author_ID = $result['author_id'];
		$this->post_type = $result['type'];
		$this->post_parent_ID = $result['parent_id'];
		$this->post_media_ID = $result['media_id'];
		$this->published_at = $result['published_at'];
		$this->created_at = $result['created_at'];
		$this->updated_at = $result['updated_at'];

		return $result;

	}

	/**
	 * Create a new post.
	 * 
	 * Returns the ID of the newly created post or
	 * returns a boolean value of false if it failed.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function create() {

		// Does the post already exist?
		if ( false !== $this->exists( $this->ID ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the insert statement.
		$query = $db->connection->prepare( 'INSERT INTO ' . $db->prefix . 'posts ( title, content, path, status, type, tags, author_id, parent_id, media_id, published_at, created_at, updated_at ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )' );

		// Did we get a published date?
		if ( '' == $this->published_at ) {

			$this->published_at = date( 'Y-m-d H:i:s' );

		}

		// Set the timestamps.
		$this->published_at = date( 'Y-m-d H:i:s', strtotime( $this->published_at ) );
		$this->created_at = date( 'Y-m-d H:i:s' );
		$this->updated_at = date( 'Y-m-d H:i:s' );

		// Filter and trim the post title and content.
		$this->post_title = filter_text( trim( $this->post_title ) );
		$this->post_content = filter_text( ltrim( $this->post_content ) );

		// Filter the path.
		$this->post_path = create_path( $this->post_path );

		// Has the path been filtered to blank?
		if ( '' == $this->post_path ) {

			// Set the path as the title and filter again.
			$this->post_path = create_path( $this->post_title );

		}

		// Did we have a blank title after all that?
		if ( '' == $this->post_path ) {

			// Set it as the current timestamp.
			$this->post_path = date( 'YmdHis' );

		}

		// Is the post status valid?
		if ( ! in_array( $this->post_status, array( 'publish', 'draft' ) ) ) {

			// Fallback to saving as draft.
			$this->post_status = 'draft';

		}

		// Do we have any tags?
		if ( '' != $this->post_tags ) {

			// Convert the tags into an array.
			$post_tags = explode( ',', $this->post_tags );

			// Create tag list.
			$tags = array();

			// Save each individual tag.
			foreach ( $post_tags as $post_tag ) {

				$tag = new Tag;

				// Set the tag values.
				$tag->tag_name = $post_tag;

				// Try and save it.
				if ( false !== $tag->save() && ! in_array( $tag->tag_name, $tags, true ) ) {

					// Add it to the list.
					$tags[] = $tag->tag_name;

				}

			}

			// Convert to the tags JSON.
			$this->post_tags = json_encode( $tags );

		}

		// Create a new user instance.
		$user = new User;

		// Does the author exist?
		if ( ! $user->exists( $this->post_author_ID ) ) {

			// Invalid so default to the current user.
			$this->post_author_ID = get_current_user_id();

		}

		// Bind parameters to the query.
		$query->bindParam( 1, $this->post_title );
		$query->bindParam( 2, $this->post_content );
		$query->bindParam( 3, $this->post_path );
		$query->bindParam( 4, $this->post_status );
		$query->bindParam( 5, $this->post_type );
		$query->bindParam( 6, $this->post_tags );
		$query->bindParam( 7, $this->post_author_ID );
		$query->bindParam( 8, $this->post_parent_ID );
		$query->bindParam( 9, $this->post_media_ID );
		$query->bindParam( 10, $this->published_at );
		$query->bindParam( 11, $this->created_at );
		$query->bindParam( 12, $this->updated_at );

		// Execute the query.
		$query->execute();

		// Convert tags back to an array.
		$this->post_tags = json_decode( $this->post_tags, true );

		// Set the new inserted ID.
		$this->ID = $db->connection->lastInsertId();

		return $this->ID;

	}

	/**
	 * Save the post instance.
	 * 
	 * Saves the current post instance to the database
	 * or creates a new post if the ID doesn't exist.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function save() {

		// Does the post already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return $this->create();

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the update statement.
		$query = $db->connection->prepare( 'UPDATE ' . $db->prefix . 'posts SET title = ?, content = ?, path = ?, status = ?, type = ?, tags = ?, author_id = ?, parent_id = ?, media_id = ?, published_at = ?, created_at = ?, updated_at = ? WHERE ID = ?' );

		// Set the updated timestamp.
		$this->published_at = date( 'Y-m-d H:i:s', strtotime( $this->published_at ) );
		$this->updated_at = date( 'Y-m-d H:i:s' );

		// Filter and trim the post title and content.
		$this->post_title = filter_text( trim( $this->post_title ) );
		$this->post_content = filter_text( ltrim( $this->post_content ) );

		// Filter the path.
		$this->post_path = create_path( $this->post_path );

		// Has the path been filtered to blank?
		if ( '' == $this->post_path ) {

			// Set the path as the title and filter again.
			$this->post_path = create_path( $this->post_title );

		}

		// Did we have a blank title after all that?
		if ( '' == $this->post_path ) {

			// Set it as the current timestamp.
			$this->post_path = date( 'YmdHis' );

		}

		// Is the post status valid?
		if ( ! in_array( $this->post_status, array( 'publish', 'draft' ) ) ) {

			// Fallback to saving as draft.
			$this->post_status = 'draft';

		}

		// Do we have any tags?
		if ( '' != $this->post_tags ) {

			// Convert the tags into an array.
			$post_tags = explode( ',', $this->post_tags );

			// Create tag list.
			$tags = array();

			// Save each individual tag.
			foreach ( $post_tags as $post_tag ) {

				$tag = new Tag;

				// Set the tag values.
				$tag->tag_name = $post_tag;

				// Try and save it.
				if ( false !== $tag->save() && ! in_array( $tag->tag_name, $tags, true ) ) {

					// Add it to the list.
					$tags[] = $tag->tag_name;

				}

			}

			// Convert to the tags JSON.
			$this->post_tags = json_encode( $tags );

		}

		// Create a new user instance.
		$user = new User;

		// Does the author exist?
		if ( ! $user->exists( $this->post_author_ID ) ) {

			// Invalid so default to the current user.
			$this->post_author_ID = get_current_user_id();

		}

		// Bind parameters to the query.
		$query->bindParam( 1, $this->post_title );
		$query->bindParam( 2, $this->post_content );
		$query->bindParam( 3, $this->post_path );
		$query->bindParam( 4, $this->post_status );
		$query->bindParam( 5, $this->post_type );
		$query->bindParam( 6, $this->post_tags );
		$query->bindParam( 7, $this->post_author_ID );
		$query->bindParam( 8, $this->post_parent_ID );
		$query->bindParam( 9, $this->post_media_ID );
		$query->bindParam( 10, $this->published_at );
		$query->bindParam( 11, $this->created_at );
		$query->bindParam( 12, $this->updated_at );
		$query->bindParam( 13, $this->ID );

		// Execute the query.
		$query->execute();

		// Convert tags back to an array.
		$this->post_tags = json_decode( $this->post_tags, true );

		return $this->ID;

	}

	/**
	 * Delete an existing post.
	 * 
	 * Permanently deletes the current post instance.
	 * Returns true is successful or false on failure.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function delete() {

		// Does the post already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the delete statement.
		$query = $db->connection->prepare( 'DELETE FROM ' . $db->prefix . 'posts WHERE ID = ? LIMIT 1' );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->ID );

		// Execute the query.
		$query->execute();

		return $this->reset( true );

	}

	/**
	 * Format an array of post data into an object.
	 * 
	 * This function is for formatting arrays of data that have
	 * been fetched directly from the database and transforming
	 * it into a proper post object.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $data The array of post data to formatted.
	 * 
	 * @return void
	 */
	public function format_array( $data = array() ) {

		// Do we have anything in this array?
		if ( is_array( $data ) && ! empty( $data ) ) {

			$this->previous_ID = $this->ID;
			$this->ID = isset( $data[ 'id' ] ) ? $data[ 'id' ] : 0;
			$this->post_title = isset( $data['title'] ) ? $data[ 'title' ] : '';
			$this->post_content = isset( $data['content'] ) ? $data[ 'content' ] : '';
			$this->post_path = isset( $data['path'] ) ? $data[ 'path' ] : '';
			$this->post_status = isset( $data['status'] ) ? $data[ 'status' ] : '';
			$this->post_type = isset( $data['type'] ) ? $data[ 'type' ] : '';
			$this->post_tags = isset( $data['tags'] ) ? json_decode( $data['tags'], true ) : array();
			$this->post_author_ID = isset( $data[ 'author_id' ] ) ? $data[ 'author_id' ] : 0;
			$this->post_parent_ID = isset( $data[ 'parent_id' ] ) ? $data[ 'parent_id' ] : 0;
			$this->post_media_ID = isset( $data[ 'media_id' ] ) ? $data[ 'media_id' ] : 0;
			$this->published_at = isset( $data['published_at'] ) ? $data[ 'published_at' ] : '';
			$this->created_at = isset( $data['created_at'] ) ? $data[ 'created_at' ] : '';
			$this->updated_at = isset( $data['updated_at'] ) ? $data[ 'updated_at' ] : '';

		}

	}

	/**
	 * Check a post exists.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The post ID.
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
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'posts WHERE ID = ? LIMIT 1' );

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
	 * Switches to the previous post.
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

		// Does the previous post already exist?
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
		$this->post_title = '';
		$this->post_content = '';
		$this->post_path = '';
		$this->post_status = '';
		$this->post_type = '';
		$this->post_tags = array();
		$this->post_author_ID = 0;
		$this->post_parent_ID = 0;
		$this->post_media_ID = 0;
		$this->published_at = '';
		$this->created_at = '';
		$this->updated_at = '';

		return true;

	}

}

