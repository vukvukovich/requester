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
class Requester_WPCLI {

	public function __construct() {

			// example constructor called when plugin loads
	}

	public function exposed_function() {

			// give output
			WP_CLI::success( 'hello from exposed_function() !' );

	}

	public function exposed_function_with_args( $args, $assoc_args ) {

			// process arguments

			// do cool stuff

			// give output
			WP_CLI::success( 'hello from exposed_function_with_args() !' );

	}

}

WP_CLI::add_command( 'requester', 'ExamplePluginWPCLI' );

}
