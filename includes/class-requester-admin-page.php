<?php
/**
 * Class for plugin admin page
 *
 * @since     1.0.0
 * @package   Requester
 */

/**
 * Admin Page class
 */
class Requester_Admin_Page {
	/**
	 * Initialize plugin admin page
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'in_admin_header', array( __CLASS__, 'get_header' ), 100 );
		add_action( 'admin_menu', array( __CLASS__, 'register' ) );
	}

	/**
	 * Register admin page.
	 *
	 * @return void
	 */
	public static function register() {
		add_menu_page(
			esc_html__( 'Requester admin page', 'requester' ),
			esc_html( 'Requester' ),
			'manage_options',
			'requester',
			array( __CLASS__, 'render' ),
			'dashicons-welcome-widgets-menus',
			99
		);
	}

	/**
	 * Notice bar display message.
	 *
	 * @since 2.3.0
	 */
	public static function get_header() {
		$plugin = Requester::get_instance();

		// Bail if we're not on a plugin admin page.
		if ( ! $plugin->is_admin_page() ) {
			return;
		}

		printf(
			'<div id="requester-header">
			    <img src="%simages/logo.svg" alt="%s"/>
		    </div>',
			esc_url( $plugin->get_assets_url() ),
			esc_html( 'Requester' )
		);
	}

	/**
	 * Render admin page.
	 *
	 * @return void
	 */
	public static function render() {
		?>
		<div id="requester-page">
			<div class="menu">
				<a href="#" class="tab active">
					<?php esc_html_e( 'General', 'requester' ); ?>
				</a>
			</div>
			<div class="content">
				<h1 class="screen-reader-text">
					<?php esc_html_e( 'Requester admin page.', 'requester' ); ?>
				</h1>
				<div class="row">
					<h2><?php esc_html_e( 'Requester admin page.', 'requester' ); ?></h2>
				</div>
				<div class="row-big">
					<?php echo Requester_Shortcodes::return_data_via_ajax(); // phpcs:ignore ?>
				</div>
				<p class="wp-mail-smtp-submit">
				<button id="requester-refresh-button" type="submit" class="requester-refresh-button">
					<span class="button-label"><?php esc_attr_e( 'Refresh', 'requester' ); ?></span>
					<img src="<?php echo esc_url( Requester::get_assets_url() . 'images/button-loader.svg' ); ?>" alt="<?php esc_attr_e( 'Loading', 'requester' ); ?>" class="button-loader">
				</button>
				</p>
			</div>
		</div>
		<?php
	}
}
