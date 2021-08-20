<?php
/**
 * Plugin shortcodes related class
 *
 * @since     1.0.0
 * @package   Requester
 */

/**
 * Shortcodes class
 */
class Requester_Shortcodes {
	/**
	 * Initialize plugin shortcodes
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'requester', array( __CLASS__, 'return_data_via_ajax' ) );
	}

	/**
	 *  [requester] Shortcode callback
	 *
	 *  Returns data from the "return_data" ajax endpoint.s
	 *
	 * @return string
	 */
	public static function return_data_via_ajax() {
		wp_enqueue_style( 'requester' );
		wp_enqueue_script( 'requester' );

		return <<<HTML
			<div id="requester-data">
				<div id="table-loader" style="display:none">
					<div class="rect1"></div>
					<div class="rect2"></div>
					<div class="rect3"></div>
					<div class="rect4"></div>
					<div class="rect5"></div>
				</div>
			</div>	
HTML;
	}
}
