<?php
/**
 * Main plugin WP-CLI class file
 *
 * @since     1.0.0
 * @package   Requester
 */

/**
 * Requester plugin CLI commands
 */
class Requester_CLI_Commands {
	/**
	 * Flush plugin cache
	 */
	public function flush() {
		if ( Requester::flush_cache() ) {
			WP_CLI::success( 'Plugin data deleted. You may now refresh the table.' );
		} else {
			WP_CLI::warning( 'Cache is empty.' );
		}
	}

	/**
	 * Override plugin cache expiration time
	 *
	 * @param int $args Time in seconds.
	 */
	public function override( $args ) {
		$time = (int) $args[0];

		if ( $time <= 0 ) {
			WP_CLI::error( 'Enter time in seconds.', true );
		}

		if ( Requester::set_cache_expiration( $time ) ) {
			WP_CLI::success( 'Cache expiration time set.' );
		}
	}
}
