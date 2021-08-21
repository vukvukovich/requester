<?php
/**
 * Requester plugin
 *
 * @since             1.0.0
 * @package           Requester
 * @author            Vuk Vukovic
 * @copyright         2021 Vuk Vukovic
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Requester
 * Plugin URI:        https://requester.com/
 * Description:       AM test project plugin for making simple requests.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Vuk Vukovic
 * Author URI:        https://vukvukovic.com
 * Text Domain:       requester
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * Plugin autoloader
 *
 * @param string $classname Class name.
 *
 * @return void
 */
spl_autoload_register(
	function( $classname ) {
		if ( strpos( $classname, 'Requester' ) !== 0 ) {
				return;
		}

		$file_name = str_replace(
			array( 'Requester', '_' ),
			array( '', '-' ),
			strtolower( $classname )
		);

		$dirs = array( '/includes', '/includes/cli' );

		foreach ( $dirs as $dir ) {
			$file = dirname( __FILE__ ) . $dir . '/class-' . $file_name . '.php';

			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	}
);

Requester::get_instance();
