<?php

/**
 * Move the contents of a directory.
 * 
 * Similar to the `copy()` function but copies the entire
 * contents of a directory on the server from one location
 * to another recursively.
 * 
 * The following source code has been adapted from the
 * PHP documentation website. It is licenced under
 * Creative Commons 3.0.
 * 
 * @link http://php.net/manual/en/function.copy.php#91010
 * 
 * @param string $from The directory to move from.
 * @param string $to   The directory to move to.
 * 
 * @return boolean
 */
function Copydir( $from, $to ) {

	// Use this directory.
	$directory = opendir( $from );

	// Can we create the source directory?
	if ( ! file_exists( $to ) && ! mkdir( $to ) ) {

		return false;

	}

	// Loop through each folder and file.
	while ( false !== ( $file = readdir( $directory ) ) ) {

		// Don't include certain files.
		if ( ! in_array( $file, array( '.', '..', '.DS_Store', 'Thumbs.db' ), true ) ) {

			// Is it a directory or file?
			if ( is_dir( $from . '/' . $file ) ) {

				// Repeat the process.
				Copydir( $from . '/' . $file, $to . '/' . $file );

			} else {

				// Copy the file.
				copy( $from . '/' . $file, $to . '/' . $file );

			}

		}

	}

	// Close connection.
	@closedir( $directory );

}
