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
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {

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
			    <img src="%s/images/logo.svg" alt="Requester"/>
		    </div>',
			esc_url( $plugin->get_assets_url() )
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
                        <?php echo Requester::get_instance()->return_data_via_ajax(); // phpcs:ignore ?>
					</div>
					<p class="wp-mail-smtp-submit">
					<button id="requester-refresh-button" type="submit" class="requester-refresh-button">
						<span class="button-label"><?php esc_attr_e( 'Refresh', 'requester' ); ?></span>
						<img src="<?php echo esc_url( Requester::get_assets_url() . '/images/button-loader.svg' ); ?>" alt="Loading" class="button-loader">
					</button>
					</p>
				</div>
		</div>

		<?php
	}
}
