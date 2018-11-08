<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * Post types handler.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class PostTypes {

	/**
	 * Get the application post types.
	 * 
	 * Returns the specified post type if a slug is
	 * provided otherwise defaults to returning all
	 * currently registered post types.
	 * 
	 * Returns false if the specified post type is
	 * not found in the array.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $slug The post type slug to return.
	 * 
	 * @return array
	 */
	public function get( $slug = '' ) {

		global $_post_types;

		// Did we get a slug?
		if ( '' != $slug ) {

			// Is the slug a post type?
			if ( ! isset( $_post_types[ $slug ] ) ) {

				return false;

			}

			return $_post_types[ $slug ];

		}

		return $_post_types;

	}

	/**
	 * Create a new post type.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $args The array of post type arguments.
	 * 
	 * @return boolean
	 */
	public function create( $args = array() ) {

		global $_post_types;

		// Setup post type defaults.
		$defaults = array(
			'id' => '',
			'path' => '',
			'has_archive' => true,
			'show_in_url' => true,
			'show_in_api' => true,
			'labels' => array(
				'name' => '',
				'singular' => '',
				'plural' => ''
			),
			'_is_system' => false
		);

		// Merge the new post type in.
		$args = array_merge( $defaults, $args );

		// Clean up the slug.
		$args[ 'id' ] = create_path( $args[ 'id' ] );

		// Bail if it already exists.
		if ( isset( $_post_types[ $args[ 'id' ] ] ) ) {

			return false;

		}

		// Add to the post type list.
		$_post_types[ $args[ 'id' ] ] = $args;

		return true;

	}

	/**
	 * Delete an existing post type.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $slug The post type slug to delete.
	 * 
	 * @return boolean
	 */
	public function delete( $slug = '' ) {

		global $_post_types;

		// Does the post type exist?
		if ( isset( $_post_types[ $slug ] ) && false === $_post_types[ $slug ][ '_is_system' ] ) {

			// Remove it.
			unset( $_post_types[ $slug ] );

			return true;

		}

		return false;

	}

}

