<?php
/**
 * Plugin uninstall file
 *
 * @since     1.0.0
 * @package   Requester
 */

// If uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'requester_cache_expiration' );
delete_transient( 'requester' );
