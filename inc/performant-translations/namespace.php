<?php
/**
 * Figuren_Theater Performance Performant_Translations.
 *
 * @package figuren-theater/ft-performance
 */

namespace Figuren_Theater\Performance\Performant_Translations;

use FT_VENDOR_DIR;
use function add_action;

const BASENAME   = 'performant-translations/performant-translations.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin(): void {

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
}
