<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * Core controller class.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Controller {

	/**
	 * The collection of routes.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $routes = array();

	/**
	 * Fetch an application route.
	 * 
	 * Returns the specified route if a valid path
	 * is given or returns every registered route if
	 * no path is provided.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $path The url path to fetch.
	 * 
	 * @return array|boolean
	 */
	public function get_route( $path = '' ) {

		// Are we looking for one route?
		if ( '' != $path ) {

			// Check that route exists.
			if ( in_array( $path, $this->routes ) ) {

				return $this->routes[ $path ];

			}

			return false;

		}

		return $this->routes;

	}

	/**
	 * Register a GET based route.
	 * 
	 * Helper function for `new()`.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string  $path     The url path of the route.
	 * @param array   $callback The route callback function.
	 * @param boolean $auth     The authorisation flag for this view.
	 * 
	 * @return boolean
	 */
	public function get( $path, $callback, $auth = true ) {

		return $this->create_route( $path, $callback, 'get', $auth );

	}

	/**
	 * Register a POST based route.
	 * 
	 * Helper function for `new()`.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string  $path     The url path of the route.
	 * @param array   $callback The route callback function.
	 * @param boolean $auth     The authorisation flag for this view.
	 * 
	 * @return boolean
	 */
	public function post( $path, $callback, $auth = true ) {

		return $this->create_route( $path, $callback, 'post', $auth );

	}

	/**
	 * Register a application route.
	 * 
	 * Registers a new application route if the
	 * given path does not already exist. Returns
	 * true if successful or false on failure.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @param string  $path     The url path of the route.
	 * @param array   $callback The route callback function.
	 * @param string  $method   The request method (either get or post).
	 * @param boolean $auth     The authorisation flag for this view.
	 * 
	 * @return boolean
	 */
	private function create_route( $path, $callback, $method = 'get', $auth = true ) {

		// Force to lowercase.
		$method = strtolower( $method );

		// Force invalid methods to get.
		if ( 'get' != $method && 'post' != $method ) {

			$method = 'get';

		}

		// Convert to an array.
		$path = explode( '/', $path );

		// Temporary URL holder.
		$temp_url = array();

		// Loop through and clean each URL part.
		foreach ( $path as $part ) {

			// Only wipe none parameter parts.
			if ( ':param' != $part ) {

				// Clean the part of the URL.
				$part = sanitise_text( $part, '~[^A-Za-z0-9_-]~' );

			}

			if ( '' != $part ) {

				$temp_url[] = $part;

			}

		}

		// Convert back into a proper path.
		$path = implode( '/', $temp_url );

		// Trim and add slashes correctly.
		$path = '/' . trim( $path, '/' ) . '/';

		// Only accept true or false for the auth parameter.
		$auth = ( false === $auth ) ? $auth : true;

		// Add to the array of routes.
		$this->routes[ $path ] = array(
			'url' => $path,
			'callback' => $callback,
			'method' => $method,
			'auth' => $auth
		);

		return true;

	}

	/**
	 * Unregister an application route.
	 * 
	 * Removes a registered application route and returns
	 * true, or returns false if the route is invalid.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $path The url path of the route.
	 * 
	 * @return boolean
	 */
	public function delete_route( $path ) {

		// Check if the route exists by it's URL.
		if ( in_array( $url, $this->routes ) ) {

			// Unset the array index.
			unset( $this->routes[ $url ] );

			return true;

		}

		return false;

	}

	/**
	 * Load the selected route.
	 * 
	 * Loads the selected route from the page request
	 * and does a callback to the registered method.
	 * 
	 * @since 0.1.0
	 * 
	 * @param object $request The current request.
	 * 
	 * @return mixed
	 */
	public function load_route( $request ) {

		// Default selected route.
		$selected = false;

		// Get the page request.
		$url = '/' . trim( $request->path, '/' ) . '/';

		// Setup the ids array.
		$ids = array();

		// Loop through the existing routes.
		foreach ( $this->routes as $route ) {

			// Convert the placeholders to regex.
			$current_route = str_replace( ':param', '[A-Za-z0-9-_]+', $route['url'] );
			$current_route = '~^' . $current_route . '$~';

			// Is this the route we're looking for?
			if ( preg_match( $current_route, $url ) ) {

				// Set the selected route.
				$selected = $route;

				// Convert the route path into parts.
				$route_parts = explode( '/', $route['url'] );

				// Convert the selected URL into parts.
				$url_parts = explode( '/', $url );

				// Collect all ids.
				foreach ( $route_parts as $key => $part ) {

					// Is this a reference placeholder?
					if ( ':param' == $part ) {

						// Add to the ids collection.
						$ids[ $part ] = $url_parts[ $key ];

					}

				}

			}

		}

		// Did we not find a view callback?
		if ( false === $selected ) {

			return self::redirect('system/not-found/');

		}

		// Are we not even authorised?
		if ( true !== $selected['auth'] ) {

			return self::redirect('system/not-authorised/');

		}

		// Did we get an incorrect HTTP request type?
		if ( 'get' != $selected['method'] && ( 'post' != $selected['method'] && 'POST' != $_SERVER['REQUEST_METHOD'] ) ) {

			return self::redirect('system/bad-request/');

		}

		// Did we find a valid route?
		if ( is_callable( $selected['callback'][0], $selected['callback'][1] ) ) {

			// Rune the route callback method.
			call_user_func_array( $selected['callback'], array( $ids ) );

		} else {

			return self::redirect('system/unknown-error/');

		}

		die();

	}

	/**
	 * Redirect to another location.
	 * 
	 * Redirects the user the specified web address
	 * which may not necessarily be an internal page.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $path   The path to redirect to.
	 * @param int    $status The HTTP status to respond with.
	 * 
	 * @return void
	 */
	public static function redirect( $path, $status = 200 ) {

		// Is this a valid URL?
		if ( false === filter_var( $path, FILTER_VALIDATE_URL ) ) {

			// Clean the path up.
			$path = sanitise_path( $path );

			// Create the URL with the path.
			$path = home_url( $path );

		}

		// Redirect to the given URL.
		header( "Location: {$path}" );

		die();

	}

	/**
	 * Return the selected view templates.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string  $view   The selected view template.
	 * @param array   $args   The array of arguments to set in the view.
	 * @param boolean $layout The flag to determine if this view has a layout template.
	 * @param boolean $return The flag to return or require the template.
	 * 
	 * @return mixed
	 */
	public static function view( $view, $args = array(), $layout = false, $return = false ) {

		if ( file_exists( $view ) ) {

			// Should the file be returned or included.
			if ( false === $return ) {

				if ( is_array( $args ) && ! empty( $args ) ) {

					// Extract the page variables.
					extract( $args );

				}

				// Compact the view arguments.
				compact( $args );

				// Should we use a layout template?
				if ( true === $layout ) {

					// Get the directory of the view.
					$dir = dirname( $view );

					if ( file_exists( $dir . '/layout.php' ) ) {

						return require( $dir . '/layout.php' );

					}

				}

				return require( $view );

			}

			return $view;

		}

		return false;

	}

	/**
	 * Return a JSON object view.
	 * 
	 * Echoes an array converted into a JSON data object.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $data The data to return as JSON.
	 * 
	 * @return string $json
	 */
	public static function json( $data = array() ) {

		// Return nothing is invalid.
		if ( ! is_array( $data ) ) {

			$data = array();

		}

		echo json_encode( $data );

	}
	
}

