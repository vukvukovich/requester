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
	 * Plugin version
	 *
	 * @var string $version
	 */
	private static $version;

	/**
	 * Get plugin version
	 *
	 * @return string The version number of the plugin
	 */
	public static function get_version() {
		return self::$version;
	}

	/**
	 * Plugin DB version
	 *
	 * @var string $db_version
	 */
	private static $db_version;

	/**
	 * Get plugin DB version
	 *
	 * @return string The version number of the plugin database set
	 */
	public static function get_db_version() {
		return self::$db_version;
	}

	/**
	 * Plugin directory path
	 *
	 * @var string $path
	 */
	private static $path;

	/**
	 * Get plugin path
	 *
	 * @return string Plugin directory path
	 */
	public static function get_path() {
		return self::$path;
	}

	/**
	 * Plugin directory url
	 *
	 * @var string $url
	 */
	private static $url;

	/**
	 * Get plugin directory url
	 *
	 * @return string
	 */
	public static function get_url() {
		return self::$url;
	}

	/**
	 * Plugin directory url
	 *
	 * @var string $url
	 */
	private static $assets_url;

	/**
	 * Get plugin directory url
	 *
	 * @return string
	 */
	public static function get_assets_url() {
		return self::$assets_url;
	}

	/**
	 * Plugin instance
	 *
	 * @var object $instance
	 */
	private static $instance;

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
		 * Plugin default settings
		 *
		 * @var object $defaults
		 */
	private static $defaults;

	/**
	 * Get default plugin settings
	 *
	 * @return object $defaults
	 */
	public static function get_defaults() {
		return self::$defaults;
	}

	/**
	 * Plugin current user settings from DB
	 *
	 * @var object $settings
	 */
	private static $settings;

	/**
	 * Get current plugin settings from DB
	 *
	 * @return object $settings
	 */
	public static function get_settings() {
		return self::$settings;
	}

	/**
	 * Plugin nonce context
	 *
	 * @var string $nonce_context
	 */
	private static $nonce_context;

	/**
	 * Get plugin nonce context
	 *
	 * @return string
	 */
	public static function get_nonce_context() {
		return self::$nonce_context;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		self::$version       = '1.0.0';
		self::$path          = plugin_dir_path( __DIR__ );
		self::$url           = plugin_dir_url( __DIR__ );
		self::$assets_url    = self::$url . 'assets/';
		self::$nonce_context = 'requester-example-nonce';

		add_action( 'init', array( $this, 'init_hooks' ) );

		do_action( 'requester_loaded' );
	}

	/**
	 * Initialize hooks and actions
	 * (We could have used wp-util here as a dependency for requester-shortcode.js
	 *  and use wp.ajax in the script but I prefer plain JS)
	 *
	 * @return void
	 */
	public function init_hooks() {
		wp_register_style( 'requester', $this->get_style_filename(), array(), self::$version );
		wp_register_script( 'requester', self::$url . 'assets/js/requester.js', array(), self::$version, false );
		wp_localize_script(
			'requester',
			'requester',
			array(
				'nonce'    => wp_create_nonce( self::$nonce_context ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'locale'   => get_bloginfo( 'language' ),
				'is_admin' => is_admin(),
			)
		);

		$this->init_requirements();
	}

	/**
	 * Load plugin requirements.
	 *
	 * @return void
	 */
	private function init_requirements() {
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

		return self::$assets_url . $folder . '/' . $filename . '.' . $file_extension;
	}
}
