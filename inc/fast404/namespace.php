<?php
/**
 * Figuren_Theater Performance Fast404.
 *
 * @package figuren-theater/ft-performance
 */

namespace Figuren_Theater\Performance\Fast404;

use FT_VENDOR_DIR;

use function add_action;

const BASENAME   = 'fast404/fast404.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'mu_plugin_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	/**
	 * How to configure the error message (which are invisible to users)?
	 *
	 * @example define('FAST404_ERROR_MESSAGE', 'My new error message');
	 */

	/**
	 * How to configure file types?
	 *
	 * @example define('FAST404_REGEX', '/\.(?:js|css|jpg|jpeg|gif|png|webp|ico|exe|bin|dmg|woff|woff2)$/i');
	 */

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
}

