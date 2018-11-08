<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The front-end controller.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Front extends Controller {

	/**
	 * The class name reference.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $class = __CLASS__;

	/**
	 * Register routes for this controller.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function __construct() {

		// Define the controller routes.
		$this->get( '/', array( $this->class, 'index' ) );
		$this->get( '/posts/:param/', array( $this->class, 'view_post' ) );
		$this->get( '/:param/', array( $this->class, 'view_page' ) );
		$this->get( '/tags/:param/', array( $this->class, 'view_tag' ) );
		$this->get( '/404/', array( $this->class, 'not_found' ) );
		$this->get( 'login/', array( $this->class, 'missed_login' ) );
		$this->get( 'register/', array( $this->class, 'missed_register' ) );
		$this->get( 'logout/', array( $this->class, 'missed_logout' ) );

	}

	/**
	 * The blog home page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function index() {

		// Get all published posts in date order.
		$posts = get_posts(
			array(
				'where' => array(
					array(
						'key' => 'type',
						'value' => 'post',
					),
					array(
						'key' => 'status',
						'value' => 'publish',
					)
				),
				'orderby' => 'published_at',
				'order' => 'DESC',
				'limit' => blog_per_page(),
				'offset' => get_page_offset()
			),
			true
		);

		return self::view( theme_path( 'index.php' ), array( 'title' => 'Home', 'posts' => $posts ), true );

	}

	/**
	 * View a post.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function view_post( $params ) {

		// Create the post instance.
		$post = new Post;

		// Get the post id.
		$path = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		// Try and fetch the post.
		$fetch = $post->fetch( $path, 'path' );

		if ( ! $fetch || 'post' != $post->post_type || 'publish' != $post->post_status ) {

			return self::redirect( '404/' );

		}

		// Get the author for this post.
		$author = new User;
		$author->fetch( $post->post_author_ID );

		return self::view( theme_path( 'post.php' ), array( 'title' => $post->post_title, 'post' => $post, 'author' => $author ), true );

	}

	/**
	 * View a page.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function view_page( $params ) {

		// Create the page instance.
		$page = new Post;

		// Get the page id.
		$path = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		// Try and fetch the page.
		$fetch = $page->fetch( $path, 'path' );

		if ( ! $fetch || 'page' != $page->post_type || 'publish' != $page->post_status ) {

			return self::redirect( '404/' );

		}

		return self::view( theme_path( 'page.php' ), array( 'title' => $page->post_title, 'page' => $page ), true );

	}

	/**
	 * View tag archive.
	 * 
	 * @todo improve the accuracy of fetching related posts by tag.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function view_tag( $params ) {

		// Create the tag instance.
		$tag = new Tag;

		// Get the tag id.
		$path = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		// Try and fetch the tag.
		$fetch = $tag->fetch( $path, 'name' );

		if ( ! $fetch ) {

			return self::redirect( '404/' );

		}

		// Get all posts by this tag.
		$posts = get_posts(
			array(
				'where' => array(
					array(
						'key' => 'tags',
						'value' => $tag->tag_name,
						'compare' => 'LIKE'
					),
					array(
						'key' => 'type',
						'value' => 'post',
					),
					array(
						'key' => 'status',
						'value' => 'publish',
					)
				),
				'orderby' => 'published_at',
				'order' => 'DESC',
				'limit' => blog_per_page(),
				'offset' => get_page_offset()
			)
		);

		return self::view( theme_path( 'tag.php' ), array( 'title' => $tag->tag_name, 'tag' => $tag, 'posts' => $posts ), true );

	}

	/**
	 * 404 not found page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function not_found() {

		// Set the HTTP response.
		http_response_code( 404 );

		return self::view( theme_path( '404.php' ), array( 'title' => 'Page Not Found' ), true );

	}

	/**
	 * Redirect to the login page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function missed_login() {

		return self::redirect( 'auth/login/' );

	}

	/**
	 * Redirect to the register page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function missed_register() {

		return self::redirect( 'auth/register/' );

	}

	/**
	 * Redirect to the logout page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function missed_logout() {

		return self::redirect( 'auth/logout/' );

	}

}

