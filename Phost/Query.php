<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * Query handler.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Query {

	/**
	 * The query options.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $opts = array();

	/**
	 * The array of fetched items.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $items = array();

	/**
	 * The total number of items found.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $count = 0;

	/**
	 * Setup and execute the query.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $opts The post query search options.
	 * 
	 * @return void
	 */
	public function __construct( $opts = array() ) {

		// Set the post argument defaults.
		$defaults = array(
			'table' => 'posts',
			'where' => array(),
			'order' => 'DESC',
			'orderby' => 'ID',
			'limit' => blog_per_page(),
			'offset' => 0
		);

		// Merge the query with the defaults.
		$this->opts = array_merge( $defaults, $opts );

		// Clean up some options safely.
		$this->opts[ 'order' ] = strtoupper( $this->opts[ 'order' ] );
		$this->opts[ 'order' ] = ( in_array( $this->opts[ 'order' ], array( 'DESC', 'ASC' ), true ) ) ? $this->opts[ 'order' ] : 'DESC';
		$this->opts[ 'limit' ] = (int) $this->opts[ 'limit' ];
		$this->opts[ 'offset' ] = (int) $this->opts[ 'offset' ];

		// Fetch items.
		$this->query();

		// Count items.
		$this->query( true );

	}

	/**
	 * Executes the query on the database.
	 * 
	 * @since 0.1.0
	 * 
	 * @param boolean $count Flag to fetch or count the results.
	 * 
	 * @return boolean
	 */
	private function query( $count = false ) {

		// Create a new connection.
		$db = new Database;

		// Are there any where clauses?
		if ( empty( $this->opts[ 'where' ] ) ) {

			$where = '';

		} else {

			// Setup as an array first.
			$where = array();

			// Setup param swaps.
			$swaps = array();

			// Loop through and add each where clause.
			foreach ( $this->opts[ 'where' ] as $where_is ) {

				// Create where defaults.
				$where_defs = array(
					'key' => '1',
					'value' => 1,
					'compare' => '==',
				);

				// Merge in with the real where is.
				$where_is = array_merge( $where_defs, $where_is );

				// Force comparison to certain types.
				$where_is[ 'compare' ] = ( in_array( $where_is[ 'compare' ], array( '=', '!=', '>=', '<=', '>', '<', 'LIKE', 'LIKE_START', 'LIKE_END', 'IN' ), true ) ) ? $where_is[ 'compare' ] : '=';

				// Are we using IN?
				if ( 'IN' == $where_is[ 'compare' ] ) {

					// Add string to where array for later.
					$where[] = $where_is[ 'key' ] . ' ' . $where_is[ 'compare' ] . ' (:' . $where_is[ 'key' ] . ')';

				} else {

					// Are we using any LIKE comparisons?
					if ( in_array( $where_is[ 'compare' ], array( 'LIKE', 'LIKE_START', 'LIKE_END' ), true ) ) {

						// Add WHERE string for LIKE comparing.
						$where[] = $where_is[ 'key' ] . ' LIKE :' . $where_is[ 'key' ];

					} else {

						// Add WHERE string for comparisons.
						$where[] = $where_is[ 'key' ] . ' ' . $where_is[ 'compare' ] . ' :' . $where_is[ 'key' ];

					}

				}

				// Are we using LIKE?
				if ( in_array( $where_is[ 'compare' ], array( 'LIKE', 'LIKE_START', 'LIKE_END' ), true ) ) {

					// Add LIKE placeholder based on comparison.
					if ( 'LIKE' == $where_is[ 'compare' ] ) {

						$like_placeholder = '%' . $where_is[ 'value' ] . '%';

					} elseif ( 'LIKE_START' == $where_is[ 'compare' ] ) {

						$like_placeholder = $where_is[ 'value' ] . '%';

					} elseif ( 'LIKE_END' == $where_is[ 'compare' ] ) {

						$like_placeholder = '%' . $where_is[ 'value' ];

					}

					// Set the value as a placeholder.
					$where_is[ 'value' ] = $like_placeholder;

				} elseif ( 'IN' == $where_is[ 'compare' ] && is_array( $where_is[ 'value' ] ) ) {

					// Convert array to string.
					$where_is[ 'value' ] = implode( ', ', $where_is[ 'value' ] );

				}

				// Add to the list of swaps for the parameter bindings.
				$swaps[] = array(
					'key' => $where_is[ 'key' ],
					'value' => (string) $where_is[ 'value' ]
				);

			}

			// Convert to string for the query.
			$where = implode( ' AND ', $where );
			$where = 'WHERE ' . $where;

		}

		// Should we count or fetch the items?
		if ( false === $count ) {

			// Is there a limit set?
			if ( 0 === $this->opts[ 'limit' ] ) {

				// Prepare with a limit and offset.
				$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . $this->opts[ 'table' ] .' ' . $where . ' ORDER BY ' . $this->opts[ 'orderby' ] . ' ' . $this->opts[ 'order' ] );

			} else {

				// Prepare the standard fetch query.
				$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . $this->opts[ 'table' ] .' ' . $where . ' ORDER BY ' . $this->opts[ 'orderby' ] . ' ' . $this->opts[ 'order' ] . ' LIMIT :offset, :limit' );

				// Bind the parameters to our query.
				$query->bindParam( ':offset', $this->opts[ 'offset' ], PDO::PARAM_INT );
				$query->bindParam( ':limit', $this->opts[ 'limit' ], PDO::PARAM_INT );

			}

		} else {

			// Prepare the count query.
			$query = $db->connection->prepare( 'SELECT COUNT(*) FROM ' . $db->prefix . $this->opts[ 'table' ] .' ' . $where . ' ORDER BY ' . $this->opts[ 'orderby' ] . ' ' . $this->opts[ 'order' ] );

		}

		// Do we have any swaps?
		if ( ! empty( $swaps ) ) {

			// Loop through and bind them in.
			foreach ( $swaps as $swap ) {

				// Integer or string?
				if ( is_int( $swap[ 'value' ] ) ) {

					$query->bindParam( ':' . $swap[ 'key' ], $swap[ 'value' ], PDO::PARAM_INT );

				} else {

					$query->bindParam( ':' . $swap[ 'key' ], $swap[ 'value' ], PDO::PARAM_STR );

				}

			}

		}

		// Execute the query.
		$query->execute();

		// Fetch or counting items?
		if ( false === $count ) {

			// Set the items collection.
			$this->items = $query->fetchAll();

		} else {

			// Set the item count.
			$this->count = $query->fetchColumn();

		}

		return true;

	}

}

