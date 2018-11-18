<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The dashboard controller.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Dashboard extends Controller {

	/**
	 * The class name reference.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $class = __CLASS__;

	/**
	 * The controller templates path.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public static $path = PHOSTAPP . 'Views/Dashboard/';

	/**
	 * Register routes for this controller.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function __construct() {

		// Define the controller routes.
		$this->get( 'dashboard/', array( $this->class, 'index' ), is_logged_in() );
		$this->get( 'dashboard/about/', array( $this->class, 'about' ), is_logged_in() );
		$this->get( 'dashboard/system/', array( $this->class, 'system' ), is_logged_in() );
		$this->post( 'dashboard/system/check-updates/', array( $this->class, 'check_updates' ), is_admin() );
		$this->post( 'dashboard/system/update-core/', array( $this->class, 'core_update' ), is_admin() );
		$this->get( 'dashboard/search/', array( $this->class, 'search' ), is_logged_in() );
		$this->get( 'dashboard/menus/', array( $this->class, 'menus' ), is_admin() );
		$this->get( 'dashboard/menus/new/', array( $this->class, 'menus_new' ), is_admin() );
		$this->post( 'dashboard/menus/save/', array( $this->class, 'menus_save' ), is_admin() );
		$this->get( 'dashboard/menus/edit/:param/', array( $this->class, 'menus_edit' ), is_admin() );
		$this->post( 'dashboard/menus/delete/:param/', array( $this->class, 'menus_delete' ), is_admin() );
		$this->get( 'dashboard/settings/', array( $this->class, 'settings' ), is_admin() );
		$this->post( 'dashboard/settings/save/', array( $this->class, 'settings_save' ), is_admin() );
		$this->get( 'dashboard/flags/', array( $this->class, 'flags' ), is_admin() );
		$this->post( 'dashboard/flags/save/', array( $this->class, 'flags_save' ), is_admin() );
		$this->get( 'dashboard/posts/', array( $this->class, 'posts' ), is_logged_in() );
		$this->get( 'dashboard/posts/new/', array( $this->class, 'posts_new' ), is_logged_in() );
		$this->post( 'dashboard/posts/save/', array( $this->class, 'posts_save' ), is_logged_in() );
		$this->get( 'dashboard/posts/edit/:param/', array( $this->class, 'posts_edit' ), is_logged_in() );
		$this->post( 'dashboard/posts/delete/:param/', array( $this->class, 'posts_delete' ), is_logged_in() );
		$this->get( 'dashboard/media/', array( $this->class, 'media' ), is_logged_in() );
		$this->post( 'dashboard/media/upload/', array( $this->class, 'media_upload' ), is_logged_in() );
		$this->get( 'dashboard/media/details/:param/', array( $this->class, 'media_details' ), is_logged_in() );
		$this->post( 'dashboard/media/delete/:param/', array( $this->class, 'media_delete' ), is_logged_in() );
		$this->get( 'dashboard/users/', array( $this->class, 'users' ), is_admin() );
		$this->get( 'dashboard/users/new/', array( $this->class, 'users_new' ), is_admin() );
		$this->post( 'dashboard/users/save/', array( $this->class, 'users_save' ), is_logged_in() );
		$this->get( 'dashboard/users/edit/:param/', array( $this->class, 'users_edit' ), is_logged_in() );
		$this->post( 'dashboard/users/delete/:param/', array( $this->class, 'users_delete' ), is_admin() );

	}

	/**
	 * The dashboard index.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function index() {

		return self::view( self::$path . 'index.php', array( 'title' => 'Dashboard' ), true );

	}

	/**
	 * System about page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function about() {

		/**
		 * Reset the CSRF token.
		 * 
		 * This page should _NEVER_ have any special actions so
		 * it can be used as a safe way to reset the CSRF token.
		 */
		create_csrf();

		return self::view( self::$path . 'about.php', array( 'title' => 'About Phost &lsaquo; Dashboard' ), true );

	}

	/**
	 * The system page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function system() {

		return self::view( self::$path . 'system.php', array( 'title' => 'System &lsaquo; Dashboard' ), true );

	}

	/**
	 * Check for software updates.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function check_updates() {

		// Try and check for updates.
		if ( App::check_system_update() ) {

			// Create a settings instance.
			$setting = new Setting;

			// Get the update flag.
			$setting->fetch( 'update_available', 'setting_key' );

			// Do we have an update available?
			if ( '0' === $setting->setting_value ) {

				// No updates today.
				register_notice( 'core_update', 'info', 'No updates are available for installation right now.' );

			} else {

				// We have an update!
				register_notice( 'core_update', 'success', 'A system update is now available for installation.' );

			}

		} else {

			// Update check failed.
			register_notice( 'core_update', 'warning', 'An error occurred whilst checking for updates.' );

		}

		return self::redirect( 'dashboard/system/' );

	}

	/**
	 * Upgrade software to latest version.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public static function core_update() {

		// Try and run the updater.
		if ( App::run_system_update() ) {

			// We've updated!
			register_notice( 'core_update', 'success', 'Successfully updated to the latest version of the software.' );

		} else {

			// Something went wrong.
			register_notice( 'core_update', 'warning', 'Failed to update the software to latest version.' );

		}

		return self::redirect( 'dashboard/system/' );

	}

	/**
	 * Dashboard search.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function search() {

		// Get all post types.
		$post_types = new PostTypes;
		$post_types = $post_types->get();

		// Setup the where options.
		$where = array();

		// Get the search query.
		$search_filter = ( get_search_query() ) ? get_search_query() : '';

		$where[] = array(
			'key' => 'title',
			'value' => $search_filter,
			'compare' => 'LIKE'
		);

		// Get the posts.
		$posts = get_posts(
			array(
				'where' => $where,
				'orderby' => 'ID',
				'order' => 'DESC',
				'limit' => blog_per_page(),
				'offset' => get_page_offset()
			),
			true
		);

		return self::view( self::$path . 'search.php', array( 'title' => 'Search &lsaquo; Dashboard', 'posts' => $posts ), true );

	}

	/**
	 * Blog menu page.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function menus() {

		// Get the menus.
		$menus = get_menus(
			array(
				'orderby' => 'ID',
				'order' => 'DESC',
				'limit' => 999,
				'offset' => 0
			),
			true
		);

		return self::view( self::$path . 'menus.php', array( 'title' => 'Menus &lsaquo; Dashboard', 'menus' => $menus ), true );

	}

	/**
	 * Create new menu.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function menus_new() {

		// Get the pages.
		$pages = get_posts(
			array(
				'where' => array(
					array(
						'key' => 'type',
						'value' => 'page'
					)
				),
				'orderby' => 'ID',
				'order' => 'DESC',
				'limit' => 999,
				'offset' => 0
			)
		);

		return self::view( self::$path . 'menus_new.php', array( 'title' => 'New Menu &lsaquo; Dashboard', 'pages' => $pages ), true );

	}

	/**
	 * Save a menu.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function menus_save() {

		// Did we get a post request?
		if ( empty( $_POST ) ) {

			return self::redirect( 'dashboard/menus/new/' );

		}

		// Create new menu instance.
		$menu = new Menu;

		// Is the ID an existing post?
		if ( isset( $_POST[ 'id' ] ) && $menu->exists( $_POST[ 'id' ] ) ) {

			// Set the new instance to the existing post.
			$menu->fetch( $_POST[ 'id' ] );

		}

		// Define the default items array.
		$items = array();

		// Do we have any menu items?
		if ( isset( $_POST[ 'item' ] ) && ! empty( $_POST[ 'item' ] ) ) {

			// Loop through each index.
			foreach ( $_POST[ 'item' ] as $item ) {

				// Is the menu item name and link set and is the link valid?
				if ( isset( $item[ 'name' ] ) && isset( $item[ 'href' ] ) && false !== filter_var( $item[ 'href' ], FILTER_VALIDATE_URL ) ) {

					// Add the menu item.
					$items[] = array(
						'name' => $item[ 'name' ],
						'href' => $item[ 'href' ]
					);

				}

			}

		}

		// Save/overwrite the new menu data.
		$menu->menu_name = ( isset( $_POST[ 'name' ] ) ) ? $_POST[ 'name' ] : $menu->menu_name;
		$menu->menu_location = ( isset( $_POST[ 'location' ] ) ) ? $_POST[ 'location' ] : $menu->menu_location;
		$menu->menu_list = ( ! empty( $items ) ) ? $items : $menu->menu_list;

		$menu->save();

		register_notice( 'menus_save', 'success', 'The menu has been saved.' );

		return self::redirect( 'dashboard/menus/edit/' . $menu->ID . '/' );

	}

	/**
	 * Edit a menu.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function menus_edit( $params ) {

		// Create the menu instance.
		$menu = new Menu;

		// Get all users.
		$users = get_users();

		// Get the menu id.
		$menu_id = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		if ( ! $menu->exists( $menu_id ) ) {

			return self::redirect( 'dashboard/menus/new/' );

		}

		$menu->fetch( $menu_id );

		return self::view( self::$path . 'menus_edit.php', array( 'title' => 'Edit Menu &lsaquo; Dashboard', 'menu' => $menu ), true );

	}

	/**
	 * Delete a menu.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function menus_delete( $params ) {

		// Get the CSRF token.
		$csrf = isset( $_GET[ 'csrf_token' ] ) ? $_GET[ 'csrf_token' ] : '';

		// Create the menu instance.
		$menu = new Menu;

		// Get the menu id.
		$menu_id = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		if ( ! $menu->exists( $menu_id ) ) {

			return self::redirect( 'dashboard/menus/' );

		}

		// Verify the CSRF token.
		if ( ! verify_csrf( $csrf ) ) {

			// Invalid CSRF token.
			register_notice( 'menus_delete', 'warning', 'An incorrect CSRF token was provided.' );

			return self::redirect( 'dashboard/menus/edit/' . $menu_id . '/' );

		}

		$menu->fetch( $menu_id );

		$menu->delete();

		register_notice( 'menus_delete', 'success', 'The menu has been permanently deleted.' );

		return self::redirect( 'dashboard/menus/' );

	}

	/**
	 * Site settings.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function settings() {

		// Create a setting instance.
		$settings = new Setting;

		// Get all themes
		$themes = get_all_themes();

		// Get all timezones.
		$timezones = DateTimeZone::listIdentifiers( DateTimeZone::ALL );

		return self::view( self::$path . 'settings.php', array( 'title' => 'Settings &lsaquo; Dashboard', 'settings' => $settings, 'themes' => $themes, 'timezones' => $timezones ), true );

	}

	/**
	 * Save settings.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function settings_save() {

		// Get all site settings.
		$settings = get_settings(
			array(
				'orderby' => 'ID',
				'order' => 'ASC',
				'limit' => 0,
				'offset' => 0
			)
		);

		// Loop through and save each setting.
		foreach ( $settings as $setting ) {

			$setting->setting_value = ( isset( $_POST[ $setting->setting_key ] ) ) ? $_POST[ $setting->setting_key ] : $setting->setting_value;

			$setting->save();
			$setting->reset();

			// Remove from the array.
			unset( $_POST[ $setting->setting_key ] );

		}

		// Do we have any unsaved fields?
		if ( ! empty( $_POST ) ) {

			// Create a settings instance.
			$setting = new Setting;

			// Save each value.
			foreach ( $_POST as $key => $value ) {

				// Set the new settings instance up.
				$setting->setting_key = $key;
				$setting->setting_value = $value;
				$setting->autoload = 'yes';

				$setting->save();
				$setting->reset();

				// Remove from the array.
				unset( $_POST[ $key ] );

			}

		}

		register_notice( 'settings_save', 'success', 'The settings have been saved.' );

		return self::redirect( 'dashboard/settings/' );

	}

	/**
	 * Site flags.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function flags() {

		register_notice( 'flags', 'info', 'Be careful. Flags let you to use experimental features early that may cause problems.', false, true );

		// Create a flag instance.
		$flags = new Setting;

		return self::view( self::$path . 'flags.php', array( 'title' => 'Flags &lsaquo; Dashboard', 'flags' => $flags ), true );

	}

	/**
	 * Save flags.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function flags_save() {

		// Get all site settings.
		$flags = get_settings(
			array(
				'orderby' => 'ID',
				'order' => 'ASC',
				'limit' => 0,
				'offset' => 0
			)
		);

		// Loop through and save each setting.
		foreach ( $flags as $flag ) {

			$flag->setting_value = ( isset( $_POST[ $flag->setting_key ] ) ) ? $_POST[ $flag->setting_key ] : $flag->setting_value;

			$flag->save();
			$flag->reset();

			// Remove from the array.
			unset( $_POST[ $flag->setting_key ] );

		}

		// Do we have any unsaved fields?
		if ( ! empty( $_POST ) ) {

			// Create a settings instance.
			$flag = new Setting;

			// Save each value.
			foreach ( $_POST as $key => $value ) {

				// Set the new settings instance up.
				$flag->setting_key = $key;
				$flag->setting_value = $value;
				$flag->autoload = 'yes';

				$flag->save();
				$flag->reset();

				// Remove from the array.
				unset( $_POST[ $key ] );

			}

		}

		register_notice( 'flags_save', 'success', 'The flags have been saved.' );

		return self::redirect( 'dashboard/flags/' );

	}

	/**
	 * View all posts.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function posts() {

		// Get all post types.
		$post_types = new PostTypes;
		$post_types = $post_types->get();

		// Setup the where options.
		$where = array();

		// Setup the filter values.
		$type_filter = ( isset( $_GET[ 'type' ] ) ) ? $_GET[ 'type' ] : '';
		$status_filter = ( isset( $_GET[ 'status' ] ) ) ? $_GET[ 'status' ] : '';

		// Was a type filter set?
		if ( isset( $post_types[ $type_filter ] ) ) {

			$where[] = array(
				'key' => 'type',
				'value' => $type_filter
			);

		}

		// Was a status filter set?
		if ( in_array( $status_filter, array( 'draft', 'publish' ), true ) ) {

			$where[] = array(
				'key' => 'status',
				'value' => $status_filter
			);

		}

		// Get the posts.
		$posts = get_posts(
			array(
				'where' => $where,
				'orderby' => 'ID',
				'order' => 'DESC',
				'limit' => blog_per_page(),
				'offset' => get_page_offset()
			),
			true
		);

		return self::view( self::$path . 'posts.php', array( 'title' => 'Posts &lsaquo; Dashboard', 'posts' => $posts, 'post_types' => $post_types, 'type_filter' => $type_filter, 'status_filter' => $status_filter ), true );

	}

	/**
	 * Create new post.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function posts_new() {

		// Get the users.
		$users = get_users();

		// Get all post types.
		$post_types = new PostTypes;
		$post_types = $post_types->get();

		return self::view( self::$path . 'posts_new.php', array( 'title' => 'New Post &lsaquo; Dashboard', 'users' => $users, 'post_types' => $post_types ), true );

	}

	/**
	 * Save a post.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function posts_save() {

		// Did we get a post request?
		if ( empty( $_POST ) ) {

			return self::redirect( 'dashboard/posts/new/' );

		}

		// Create new post instance.
		$post = new Post;

		// Is the ID an existing post?
		if ( isset( $_POST[ 'id' ] ) && $post->exists( $_POST[ 'id' ] ) ) {

			// Set the new instance to the existing post.
			$post->fetch( $_POST[ 'id' ] );

		}

		// Save/overwrite the new post data.
		$post->post_title = ( isset( $_POST[ 'title' ] ) ) ? $_POST[ 'title' ] : $post->post_title;
		$post->post_content = ( isset( $_POST[ 'content' ] ) ) ? $_POST[ 'content' ] : $post->post_content;
		$post->post_path = ( isset( $_POST[ 'path' ] ) ) ? $_POST[ 'path' ] : $post->post_path;
		$post->post_status = ( isset( $_POST[ 'status' ] ) ) ? $_POST[ 'status' ] : $post->post_status;
		$post->post_type = ( isset( $_POST[ 'type' ] ) ) ? $_POST[ 'type' ] : $post->post_type;
		$post->post_tags = ( isset( $_POST[ 'tags' ] ) ) ? $_POST[ 'tags' ] : $post->post_tags;
		$post->post_author_ID = ( isset( $_POST[ 'author_id' ] ) ) ? $_POST[ 'author_id' ] : $post->post_author_ID;
		$post->post_parent_ID = ( isset( $_POST[ 'parent_id' ] ) ) ? $_POST[ 'parent_id' ] : $post->post_parent_ID;
		$post->post_media_ID = ( isset( $_POST[ 'media_id' ] ) ) ? $_POST[ 'media_id' ] : $post->post_media_ID;
		$post->published_at = ( isset( $_POST[ 'published_at' ] ) ) ? $_POST[ 'published_at' ] : $post->published_at;

		$post->save();

		register_notice( 'posts_save', 'success', 'The post has been saved.' );

		return self::redirect( 'dashboard/posts/edit/' . $post->ID . '/' );

	}

	/**
	 * Edit a post.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function posts_edit( $params ) {

		// Create the post instance.
		$post = new Post;

		// Get all users.
		$users = get_users();

		// Get the post id.
		$post_id = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		if ( ! $post->exists( $post_id ) ) {

			return self::redirect( 'dashboard/posts/new/' );

		}

		$post->fetch( $post_id );

		// Get all post types.
		$post_types = new PostTypes;
		$post_types = $post_types->get();

		return self::view( self::$path . 'posts_edit.php', array( 'title' => 'Edit Post &lsaquo; Dashboard', 'post' => $post, 'users' => $users, 'post_types' => $post_types ), true );

	}

	/**
	 * Delete a post.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function posts_delete( $params ) {

		// Get the CSRF token.
		$csrf = isset( $_GET[ 'csrf_token' ] ) ? $_GET[ 'csrf_token' ] : '';

		// Create the post instance.
		$post = new Post;

		// Get the post id.
		$post_id = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		if ( ! $post->exists( $post_id ) ) {

			return self::redirect( 'dashboard/posts/' );

		}

		// Verify the CSRF token.
		if ( ! verify_csrf( $csrf ) ) {

			// Invalid CSRF token.
			register_notice( 'posts_delete', 'warning', 'An incorrect CSRF token was provided.' );

			return self::redirect( 'dashboard/posts/edit/' . $post_id . '/' );

		}

		$post->fetch( $post_id );

		$post->delete();

		register_notice( 'posts_delete', 'success', 'The post has been permanently deleted.' );

		return self::redirect( 'dashboard/posts/' );

	}

	/**
	 * The media library.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function media() {

		// Get all media.
		$media = get_media(
			array(
				'orderby' => 'uploaded_at',
				'order' => 'DESC',
				'limit' => blog_per_page(),
				'offset' => get_page_offset()
			),
			true
		);

		return self::view( self::$path . 'media.php', array( 'title' => 'Media &lsaquo; Dashboard', 'media' => $media ), true );

	}

	/**
	 * Upload new media.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function media_upload() {

		// Try and upload each file.
		$upload = prepare_file_upload( $_FILES );

		register_notice( 'media_upload', 'success', 'The media file has been uploaded.' );

		return self::redirect( 'dashboard/media/' );

	}

	/**
	 * View a file.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function media_details( $params ) {

		// Create the media instance.
		$media = new Media;

		// Get the media id.
		$media_id = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		if ( ! $media->exists( $media_id ) ) {

			return self::redirect( 'dashboard/media/' );

		}

		$media->fetch( $media_id );

		return self::view( self::$path . 'media_details.php', array( 'title' => 'Media Details &lsaquo; Dashboard', 'media' => $media ), true );

	}

	/**
	 * Delete a file.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function media_delete( $params ) {

		// Get the CSRF token.
		$csrf = isset( $_GET[ 'csrf_token' ] ) ? $_GET[ 'csrf_token' ] : '';

		// Create the media instance.
		$media = new Media;

		// Get the media id.
		$media_id = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		if ( ! $media->exists( $media_id ) ) {

			return self::redirect( 'dashboard/media/' );

		}

		// Verify the CSRF token.
		if ( ! verify_csrf( $csrf ) ) {

			// Invalid CSRF token.
			register_notice( 'media_delete', 'warning', 'An incorrect CSRF token was provided.' );

			return self::redirect( 'dashboard/media/details/' . $media_id . '/' );

		}

		$media->fetch( $media_id );

		$media->delete();

		register_notice( 'media_delete', 'success', 'The media file has been permanently deleted.' );

		return self::redirect( 'dashboard/media/' );

	}

	/**
	 * View all users.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function users() {

		// Setup the where options.
		$where = array();

		// Setup the filter values.
		$type_filter = ( isset( $_GET[ 'type' ] ) ) ? $_GET[ 'type' ] : '';

		// Can we filter by type?
		if ( in_array( $type_filter, array( 'user', 'admin' ), true ) ) {

			$where[] = array(
				'key' => 'type',
				'value' => $type_filter
			);

		}

		// Get the users.
		$users = get_users(
			array(
				'where' => $where,
				'orderby' => 'ID',
				'order' => 'DESC',
				'limit' => blog_per_page(),
				'offset' => get_page_offset()
			),
			true
		);

		return self::view( self::$path . 'users.php', array( 'title' => 'Users &lsaquo; Dashboard', 'users' => $users, 'type_filter' => $type_filter ), true );

	}

	/**
	 * Create new post.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function users_new() {

		return self::view( self::$path . 'users_new.php', array( 'title' => 'New User &lsaquo; Dashboard' ), true );

	}

	/**
	 * Save a user.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function users_save() {

		// Did we get a post request?
		if ( empty( $_POST ) ) {

			return self::redirect( 'dashboard/users/new/' );

		}

		// Is this user me or an admin?
		if ( isset( $_POST[ 'id' ] ) && ! is_me( $_POST[ 'id' ] ) && ( ! is_me( $_POST[ 'id' ] ) && ! is_admin() ) ) {

			return self::redirect( 'dashboard/users/edit/' . my_id() . '/' );

		}

		// Create new user instance.
		$user = new User;

		// Is the ID an existing user?
		if ( isset( $_POST[ 'id' ] ) && $user->exists( $_POST[ 'id' ] ) ) {

			// Set the new instance to the existing user.
			$user->fetch( $_POST[ 'id' ] );

		}

		// Don't let non admins change a user's type.
		if ( ! is_me( $user->ID ) && is_admin() ) {

			$user_type = ( isset( $_POST[ 'type' ] ) && 'admin' == $_POST[ 'type' ] ) ? 'admin' : 'user';

		} else {

			$user_type = $user->user_type;

		}

		// Save/overwrite the new user data.
		$user->user_fullname = ( isset( $_POST[ 'fullname' ] ) ) ? $_POST[ 'fullname' ] : $user->user_fullname;
		$user->user_email = ( isset( $_POST[ 'email' ] ) ) ? $_POST[ 'email' ] : $user->user_email;
		$user->user_password = ( isset( $_POST[ 'password' ] ) ) ? $_POST[ 'password' ] : $user->user_password;
		$user->user_type = $user_type;
		$user->user_notify = ( isset( $_POST[ 'notify' ] ) ) ? true : false;

		// Should we notify the user?
		if ( $user->save() && $user->user_notify ) {

			// Create email message.
			$message = "Hello {$user->user_fullname},<br /><br />
Your account on " . blog_name() . " has been successfully created.<br /><br />
You can login by using the email address this message was sent to and your password which is: <strong>" . $_POST[ 'password' ] . "</strong>.<br /><br />
Please delete this email and change your password once you've logged in.<br /><br />
Thank you,<br />The team at " . blog_name() . ".";

			// Send the email!
			email( $user->user_email, '[' . blog_name() . '] Account Created', $message, true );

		}

		/**
		 * Is this user me and has my email changed?
		 * 
		 * We can't use `is_me()` here because it checks if
		 * the current user is logged in and does and authentication
		 * check which obviously fails as we're having to reset it
		 * here which isn't ideal but it's what we've got.
		 */
		if ( $_SESSION['id'] === $user->ID && false === password_verify( $user->user_email, $_COOKIE[ AUTH_COOKIE ] ) ) {

			// Reauthenticate the user.
			Auth::login_user( $user->ID, $user->user_email );

		}

		register_notice( 'users_save', 'success', 'The user account has been saved.' );

		return self::redirect( 'dashboard/users/edit/' . $user->ID . '/' );

	}

	/**
	 * Edit a user.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function users_edit( $params ) {

		// Create new users instance.
		$user = new User;

		// Get the user id.
		$user_id = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		// Is this user me or an admin?
		if ( ! is_me( $user_id ) && ( ! is_me( $user_id ) && ! is_admin() ) ) {

			return self::redirect( 'dashboard/users/edit/' . my_id() . '/' );

		}

		// Does this user exist?
		if ( ! $user->exists( $user_id ) ) {

			return self::redirect( 'dashboard/users/new/' );

		}

		$user->fetch( $user_id );

		return self::view( self::$path . 'users_edit.php', array( 'title' => 'Edit User &lsaquo; Dashboard', 'user' => $user ), true );

	}

	/**
	 * Delete a user.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $params Parameters passed in the URL.
	 * 
	 * @return mixed
	 */
	public static function users_delete( $params ) {

		// Get the CSRF token.
		$csrf = isset( $_GET[ 'csrf_token' ] ) ? $_GET[ 'csrf_token' ] : '';

		// Create the user instance.
		$user = new User;

		// Get the user id.
		$user_id = ( isset( $params[':param'] ) ) ? $params[':param'] : 0;

		if ( is_me( $user_id ) || ! $user->exists( $user_id ) ) {

			return self::redirect( 'dashboard/users/' );

		}

		// Verify the CSRF token.
		if ( ! verify_csrf( $csrf ) ) {

			// Invalid CSRF token.
			register_notice( 'users_delete', 'warning', 'An incorrect CSRF token was provided.' );

			return self::redirect( 'dashboard/users/edit/' . $user_id . '/' );

		}

		$user->fetch( $user_id );

		$user->delete();

		register_notice( 'users_delete', 'success', 'The user has been permanently deleted.' );

		return self::redirect( 'dashboard/users/' );

	}

}

