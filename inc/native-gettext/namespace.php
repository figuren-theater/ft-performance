<?php
/**
 * Figuren_Theater Performance Native_Gettext.
 *
 * @package figuren-theater/ft-performance
 */

namespace Figuren_Theater\Performance\Native_Gettext;

use Figuren_Theater;

use FT_VENDOR_DIR;
use function add_action;

const BASENAME   = 'native-gettext/native-gettext.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['native-gettext'] ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
}
