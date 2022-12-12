<?php
/**
 * Figuren_Theater Performance Native_Gettext.
 *
 * @package figuren-theater/performance/native_gettext
 */

namespace Figuren_Theater\Performance\Native_Gettext;

use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;

const BASENAME   = 'native-gettext/native-gettext.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['native-gettext'] )
		return; // early

	require_once PLUGINPATH;
}
