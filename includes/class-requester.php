<?php
/**
 * Main plugin class file
 *
 * @since     1.0.0
 * @package   Requester
 */

/**
 * Main class
 */
class Requester {
	/**
	 * Plugin instance
	 *
	 * @var object $instance
	 */
	private static $instance;

	/**
	 * Plugin version
	 *
	 * @var string $version
	 */
	private static $version;

	/**
	 * Plugin directory url
	 *
	 * @var string $url
	 */
	private static $url;

	/**
	 * Plugin directory path
	 *
	 * @var string $path
	 */
	private static $path;

	/**
	 * Plugin directory url
	 *
	 * @var string $url
	 */
	private static $assets_url;

	/**
	 * Plugin nonce context
	 *
	 * @var string $nonce_context
	 */
	private static $nonce_context;

	/**
	 * Plugin slug
	 *
	 * @var string $slug
	 */
	private static $slug;

	/**
	 * Plugin current user settings from DB
	 *
	 * @var object $settings
	 */
	private static $settings;

	/**
	 * Constructor
	 */
	private function __construct() {
		self::$version       = '1.0.0';
		self::$url           = plugin_dir_url( __DIR__ );
		self::$path          = plugin_dir_path( __DIR__ );
		self::$assets_url    = self::$url . 'assets/';
		self::$nonce_context = 'requester-example-nonce';
		self::$slug          = 'requester'; // We could have used at some point get_plugin_data(). Not really needed.

		add_action( 'init', array( $this, 'init_scripts' ) );
		add_action( 'init', array( $this, 'init_settings' ), 5 );
		add_action( 'init', array( $this, 'init_requirements' ) );
		add_action( 'cli_init', array( 'Requester_CLI', 'register_commands' ) );

		do_action( 'requester_loaded' );
	}

	/**
	 * Initialize scripts
	 *
	 * (We could have used wp-util here as a dependency for requester-shortcode.js
	 *  and use wp.ajax in the script but I prefer plain JS)
	 *
	 * @return void
	 */
	public function init_scripts() {
		wp_register_style( self::$slug, $this->get_style_filename(), array(), self::$version );
		wp_register_script( self::$slug, self::$url . 'assets/js/requester.min.js', array(), self::$version, false );
		wp_localize_script(
			self::$slug,
			self::$slug,
			array(
				'nonce'    => wp_create_nonce( self::$nonce_context ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'locale'   => get_bloginfo( 'language' ),
				'is_admin' => is_admin(),
			)
		);
	}

	/**
	 * Load plugin requirements.
	 *
	 * @return void
	 */
	public function init_requirements() {
		$requirements = array(
			'Admin_Page',
			'Ajax_Endpoints',
			'Shortcodes',
		);

		foreach ( $requirements as $requirement ) {
			( __CLASS__ . '_' . $requirement )::init();
		}
	}

	/**
	 * Initialize plugin settings
	 *
	 * @return void
	 */
	public function init_settings() {
		$option_name = 'requester_cache_expiration';

		if ( $expiration_time = get_option( $option_name ) ) { // phpcs:ignore
			self::$settings['cache_expiration'] = $expiration_time;

		} else {
			update_option( $option_name, HOUR_IN_SECONDS );
		}
	}

	/**
	 * Check if we are currently on the plugin admin page.
	 *
	 * @return bool
	 */
	public function is_admin_page() {
		if ( ! is_admin() ) {
			return false;
		}

		if ( isset( $_GET['page'] ) && 'requester' === $_GET['page'] ) { // phpcs:ignore
			return true;
		}

		return false;
	}

	/**
	 * Check if referer is plugin admin page.
	 */
	public static function is_referer() {
		if ( ! is_admin() ) {
			return false;
		}

		$referer_url = wp_get_referer();

		wp_parse_str( wp_parse_url( $referer_url )['query'], $referer );

		return 'requester' === $referer['page'];
	}

	/**
	 * Get plugin stylesheet filename
	 *
	 * @return string requester.css or requester-admin.css
	 */
	public function get_style_filename() {
		$filename = 'requester';
		$folder   = $file_extension = 'css'; // phpcs:ignore

		if ( $this->is_admin_page() ) {
			$filename .= '-admin';
		}

		return self::$assets_url . $folder . '/' . $filename . '.min.' . $file_extension;
	}

	/**
	 * Delete plugin cached data
	 *
	 * @return bool
	 */
	public static function flush_cache() {
		return delete_transient( self::$slug );
	}

	/**
	 * Set plugin cache expiration
	 *
	 * @param int $time Expiration time in seconds.
	 * @return mixed
	 */
	public static function set_cache_expiration( $time ) {
		$expiration_time = (int) $time;

		// Set expiration time and update transient if available.
		if ( update_option( 'requester_cache_expiration', $expiration_time ) ) {
			if ( $data = get_transient( self::$slug ) ) { // phpcs:ignore
				set_transient( self::$slug, $data, $expiration_time );
			}

			return true;
		}

		return false;
	}

	/**
	 * Initialize plugin
	 *
	 * @return Requester Returns Requester object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get plugin version
	 *
	 * @return string The version number of the plugin
	 */
	public static function get_version() {
		return self::$version;
	}

	/**
	 * Get plugin directory url
	 *
	 * @return string
	 */
	public static function get_url() {
		return self::$url;
	}

	/**
	 * Get plugin path
	 *
	 * @return string Plugin directory path
	 */
	public static function get_path() {
		return self::$path;
	}

	/**
	 * Get plugin directory url
	 *
	 * @return string
	 */
	public static function get_assets_url() {
		return self::$assets_url;
	}

	/**
	 * Get plugin nonce context
	 *
	 * @return string
	 */
	public static function get_nonce_context() {
		return self::$nonce_context;
	}

	/**
	 * Get plugin slug
	 *
	 * @return string
	 */
	public static function get_slug() {
		return self::$slug;
	}

	/**
	 * Get current plugin settings from DB
	 *
	 * @return object $settings
	 */
	public static function get_settings() {
		return self::$settings;
	}
}
