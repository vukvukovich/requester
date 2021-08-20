<?php
/**
 * Main plugin WP-CLI class file
 *
 * @since     1.0.0
 * @package   Requester
 */

/**
 * Plugin main WP-CLI class
 */
class Requester_CLI {
	/**
	 * Register plugin CLI commands
	 *
	 * @return void
	 */
	public static function register_commands() {
		WP_CLI::add_command( 'requester', 'Requester_CLI_Commands' );
	}
}
