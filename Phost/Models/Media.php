<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The media model.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Media extends Model {

	/**
	 * The media ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $ID = 0;

	/**
	 * The previous media ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $previous_ID = 0;

	/**
	 * The media name.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $media_name = '';

	/**
	 * The media file type.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $media_type = '';

	/**
	 * The media direcotry.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $media_dir = '';

	/**
	 * The media sizes.
	 * 
	 * This is only used for image files.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $media_sizes = array();

	/**
	 * The media author ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $media_author_ID = 0;

	/**
	 * The uploaded at timestamp.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $uploaded_at = '';

	/**
	 * The temporary $_FILES data.
	 * 
	 * The temporary file data from the $_FILES superglobal
	 * variable used for uploading new files to the system.
	 * 
	 * This should never be set manually.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $_files = array();

	/**
	 * The list of allowed file types.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @var array
	 */
	protected $_accept_types = array(
		'jpg',
		'jpeg',
		'gif',
		'png',
		'doc',
		'docx',
		'xls',
		'xlsx',
		'ppt',
		'pptx',
		'pdf',
		'txt',
		'pages',
		'numbers',
		'keynote'
	);

	/**
	 * The maximum allowed file size.
	 * 
	 * @since 0.1.0
	 * 
	 * @access protected
	 * 
	 * @var int
	 */
	protected $_max_size = 2000000;

	/**
	 * Create a new media instance.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The media ID.
	 * 
	 * @return mixed
	 */
	public function __construct( $id = 0 ) {

		if ( 0 !== $id ) {

			$this->fetch( $id );

		}

		// Set the meta type.
		$this->meta_type = 'media';

	}

	/**
	 * Fetch the selected media item.
	 * 
	 * @since 0.1.0
	 * 
	 * @param mixed  $value The media value to search for.
	 * @param string $key   The media key to search for.
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
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'media WHERE ' . $key . ' = ? LIMIT 1' );

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

		// Setup the media model.
		$this->previous_ID = $this->ID;
		$this->ID = $result['id'];
		$this->media_name = $result['name'];
		$this->media_type = $result['type'];
		$this->media_dir = $result['dir'];
		$this->media_sizes = json_decode( $result['sizes'], true );
		$this->media_author_ID = $result['author_id'];
		$this->uploaded_at = $result['uploaded_at'];
		$this->_files = array();

		return $result;

	}

	/**
	 * Uploads a new media item.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function upload() {

		// Does the media already exist?
		if ( false !== $this->exists( $this->ID ) ) {

			return false;

		}

		// Create the file path.
		$filepath = 'Content/Media/' . date( 'Y' ) . '/' . date( 'm' );

		// Create the full path to the file directory.
		$directory = PHOSTPATH . $filepath;

		// Does the directory exist?
		if ( ! file_exists( $directory ) ) {

			// Try and create it and bail if it fails.
			if ( ! mkdir( $directory, 0750, true ) ) {

				return false;

			}

		}

		// Get the real file name and extension.
		$basename = pathinfo( $this->_files[ 'name' ], PATHINFO_FILENAME );
		$baseext = pathinfo( $this->_files[ 'name' ], PATHINFO_EXTENSION );

		// Check we have a valid $_FILES data array set.
		if ( ! isset( $this->_files[ 'name' ] ) || ! isset( $this->_files[ 'type' ] ) || ! isset( $this->_files[ 'tmp_name' ] ) || ! isset( $this->_files[ 'error' ] ) || ! isset( $this->_files[ 'size' ] ) ) {

			return false;

		}

		// Was there an error reported?
		if ( 0 !== $this->_files[ 'error' ] ) {

			return false;

		}

		// Is the file below the limit?
		if ( $this->_files[ 'size' ] > $this->_max_size ) {

			return false;

		}

		// Check we're using a valid file type.
		if ( ! in_array( $baseext, $this->_accept_types, true ) ) {

			return false;

		}

		// Clean the file name.
		$filename = sanitise_text( $basename, '~[^A-Za-z0-9_-]~' );

		// Set default name if blank.
		if ( '' == $filename ) {

			$filename = 'untitled';

		}

		// Create the file name with extension.
		$filename = $filename . '-' . date('His');

		// Try and upload the file.
		$upload_file = move_uploaded_file( $this->_files[ 'tmp_name' ], $filepath . '/' . $filename . '.' . $baseext );

		// Was the file uploaded?
		if ( false === $upload_file ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the insert statement.
		$query = $db->connection->prepare( 'INSERT INTO ' . $db->prefix . 'media ( name, type, dir, sizes, author_id, uploaded_at ) VALUES ( ?, ?, ?, ?, ?, ? )' );

		// Set the media variables.
		$this->media_name = $filename;
		$this->media_type = $baseext;
		$this->media_dir = $filepath;
		$this->media_sizes = json_encode( array() );
		$this->media_author_ID = current_user_id();
		$this->uploaded_at = date( 'Y-m-d H:i:s' );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->media_name );
		$query->bindParam( 2, $this->media_type );
		$query->bindParam( 3, $this->media_dir );
		$query->bindParam( 4, $this->media_sizes );
		$query->bindParam( 5, $this->media_author_ID );
		$query->bindParam( 6, $this->uploaded_at );

		// Execute the query.
		$query->execute();

		// Set the new inserted ID.
		$this->ID = $db->connection->lastInsertId();

		// Reset the file variable.
		$this->_files = array();

		return $this->ID;

	}

	/**
	 * Permanently delete an existing media item.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function delete() {

		// Does the media already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return false;

		}

		// Build the full filename.
		$file = $this->media_dir . '/' . $this->media_name . '.' . $this->media_type;

		// Try and delete the file first.
		if ( ! unlink( $file ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the delete statement.
		$query = $db->connection->prepare( 'DELETE FROM ' . $db->prefix . 'media WHERE ID = ? LIMIT 1' );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->ID );

		// Execute the query.
		$query->execute();

		return $this->reset( true );

	}

	/**
	 * Format an array of media data into an object.
	 * 
	 * This function is for formatting arrays of data that have
	 * been fetched directly from the database and transforming
	 * it into a proper media object.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $data The array of media data to format.
	 * 
	 * @return void
	 */
	public function format_array( $data = array() ) {

		// Do we have anything in this array?
		if ( is_array( $data ) && ! empty( $data ) ) {

			$this->previous_ID = $this->ID;
			$this->ID = isset( $data[ 'id' ] ) ? $data[ 'id' ] : 0;
			$this->media_name = isset( $data['name'] ) ? $data[ 'name' ] : '';
			$this->media_type = isset( $data['type'] ) ? $data[ 'type' ] : '';
			$this->media_dir = isset( $data['dir'] ) ? $data[ 'dir' ] : '';
			$this->media_sizes = isset( $data['status'] ) ? json_decode( $data['sizes'], true ) : array();
			$this->media_author_ID = isset( $data['author_id'] ) ? $data[ 'author_id' ] : 0;
			$this->uploaded_at = isset( $data[ 'uploaded_at' ] ) ? $data[ 'uploaded_at' ] : 0;

		}

	}

	/**
	 * Checks if a media item exists.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The media ID.
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
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'media WHERE ID = ? LIMIT 1' );

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
	 * Switches to the previous media item.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function previous() {

		// Does the previous media already exist?
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
		$this->media_name = '';
		$this->media_type = '';
		$this->media_dir = '';
		$this->media_sizes = array();
		$this->media_author_ID = 0;
		$this->uploaded_at = '';

		return true;

	}

	/**
	 * Get the file name with the extension.
	 * 
	 * @since 0.1.0
	 * 
	 * @return string
	 */
	public function get_filename() {

		return $this->media_name . '.' . trim( $this->media_type, '.' );

	}

	/**
	 * Return the file URL.
	 * 
	 * @since 0.1.0
	 * 
	 * @return string
	 */
	public function get_url() {

		return home_url( $this->media_dir . '/' . $this->media_name . '.' . $this->media_type );

	}

	/**
	 * Return the file path.
	 * 
	 * @since 0.1.0
	 * 
	 * @return string
	 */
	public function get_path() {

		return PHOSTPATH . $this->media_dir . '/' . $this->media_name . '.' . $this->media_type;

	}

	/**
	 * Return the file size.
	 * 
	 * @since 0.1.0
	 * 
	 * @return string
	 */
	public function get_size() {

		// Get the file size in bytes.
		$bytes = filesize( $this->get_path() );

		// Return the size with byte type.
		if ( 1000000000000 <= $bytes ) {

			return number_format( $bytes / 1000000000000, 0 ) . ' TB';

		} elseif ( 1000000000 <= $bytes ) {

			return number_format( $bytes / 1000000000, 0 ) . ' GB';

		} elseif ( 1000000 <= $bytes ) {

			return number_format( $bytes / 1000000, 0 ) . ' MB';

		} elseif ( 1000 <= $bytes ) {

			return number_format( $bytes / 1000, 0 ) . ' KB';

		} elseif ( 1000 > $bytes && 1 < $bytes ) {

			return number_format( $bytes / 1000, 0 ) . ' bytes';

		} elseif ( 1 == $bytes ) {

			return $bytes . ' byte';

		}

	}

	/**
	 * Is the media an image file?
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function is_image() {

		// Is the media type set to an image?
		if ( in_array( $this->media_type, array( 'jpg', 'jpeg', 'gif', 'png' ) ) ) {

			return true;

		}

		return false;

	}

}

