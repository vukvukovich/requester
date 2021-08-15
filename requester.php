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
 * Plugin URI:        https://example.com/plugin-name
 * Description:       AM test project plugin for making simple requests.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Vuk Vukovic
 * Author URI:        https://requester.com
 * Text Domain:       requester
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * Load class when plugin is activated
 */
function requester_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-requester-activation.php';
	new Requester_Activation();
}

/**
 * Load class when plugin is uninstalled
 */
function requester_uninstall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-requester-uninstall.php';
	new Requester_Uninstall();
}

register_activation_hook( __FILE__, 'requester_activate' );
register_uninstall_hook( __FILE__, 'requester_uninstall' );

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

		$file = dirname( __FILE__ ) . '/includes/class-' . $file_name . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

Requester::init();
