<?php
/**
 * Figuren_Theater Performance WP_Super_Preload.
 *
 * @package figuren-theater/performance/wp_super_preload
 */

namespace Figuren_Theater\Performance\WP_Super_Preload;

use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;

const BASENAME   = 'wp-super-preload/wp-super-preload.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['wp-super-preload'] )
		return; // early

	require_once PLUGINPATH;
}
