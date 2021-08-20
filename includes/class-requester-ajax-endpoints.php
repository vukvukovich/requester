<?php
/**
 * Ajax endpoints related class
 *
 * @since     1.0.0
 * @package   Requester
 */

/**
 * Ajax endpoints class
 */
class Requester_Ajax_Endpoints {
	/**
	 * Initialize plugin ajax endpoints
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_return_data', array( __CLASS__, 'return_data' ) );
		add_action( 'wp_ajax_nopriv_return_data', array( __CLASS__, 'return_data' ) );
	}

	/**
	 * The return_data endpoint callback
	 * Returns data form the https://miusage.com/v1/challenge/1/
	 *
	 * @return void
	 */
	public function return_data() {
		check_ajax_referer( Requester::get_nonce_context(), 'nonce' );

		if ( isset( $_POST['refresh'] ) && Requester::is_referer() ) {
			delete_transient( 'requester' );
		}

		if ( false === ( $data = get_transient( 'requester' ) ) ) { // phpcs:ignore
			// This code runs when there is no valid transient set.
			$response = wp_remote_get( 'https://miusage.com/v1/challenge/1/' );

			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$data = ( new Requester_Data_Validator( $response['body'] ) )->validate();

				if ( ! $data ) {
					wp_die( wp_json_encode( array( 'error' => __( 'Invalid data.', 'requester' ) ) ) );
				}

				set_transient( 'requester', $data, HOUR_IN_SECONDS );
				wp_die( wp_json_encode( $data ) );
			}

			wp_die( wp_json_encode( array( 'error' => __( 'API data not available.', 'requester' ) ) ) );
		} else {
			wp_die( wp_json_encode( $data ) );
		}
	}
}
