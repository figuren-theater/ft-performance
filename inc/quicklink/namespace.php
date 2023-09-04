<?php
/**
 * Figuren_Theater Performance Quicklink.
 *
 * @package figuren-theater/ft-performance
 */

namespace Figuren_Theater\Performance\Quicklink;

use Figuren_Theater;
use FT_CORESITES;

use FT_VENDOR_DIR;
use function add_action;

use function add_filter;
use function get_home_url;
use function home_url;
use function wp_parse_url;

const BASENAME   = 'quicklink/quicklink.php';
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
	if ( ! $config['quicklink'] ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_filter( 'quicklink_options', __NAMESPACE__ . '\\filter_js_options' );
}

/**
 * Add the f.t platform URLs to the quicklink allow-list.
 *
 * @param  array<string, mixed> $options Array of options of the 'Quicklinks' plugin.
 *
 * @return array<string, mixed>
 */
function filter_js_options( array $options ) : array {

	$ft_ids = array_flip( FT_CORESITES );

	$options['origins'] = [
		wp_parse_url( home_url(), PHP_URL_HOST ),
		wp_parse_url( get_home_url( $ft_ids['root'] ), PHP_URL_HOST ),
		wp_parse_url( get_home_url( $ft_ids['meta'] ), PHP_URL_HOST ),
		wp_parse_url( get_home_url( $ft_ids['webs'] ), PHP_URL_HOST ),
		wp_parse_url( get_home_url( $ft_ids['mein'] ), PHP_URL_HOST ),
	];

	return $options;
}
