<?php
/**
 * Figuren_Theater Performance Fast404.
 *
 * @package figuren-theater/performance/fast404
 */

namespace Figuren_Theater\Performance\Fast404;

use FT_VENDOR_DIR;

use function add_action;

const BASENAME   = 'fast404/fast404.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'mu_plugin_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	// How to configure the error message (which are invisible to users)?
	// define('FAST404_ERROR_MESSAGE', 'My new error message');
	
	// How to configure file types?
	// define('FAST404_REGEX', '/\.(?:js|css|jpg|jpeg|gif|png|webp|ico|exe|bin|dmg|woff|woff2)$/i');
	
	require_once PLUGINPATH;
}

