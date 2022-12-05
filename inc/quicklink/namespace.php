<?php
/**
 * Figuren_Theater Performance Quicklink.
 *
 * @package figuren-theater/performance/quicklink
 */

namespace Figuren_Theater\Performance\Quicklink;

use FT_CORESITES;
use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;
use function add_filter;
use function get_home_url;
use function home_url;
use function wp_parse_url;

const BASENAME   = 'quicklink/quicklink.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['quicklink'] )
		return; // early

	require_once PLUGINPATH;

	add_filter( 'quicklink_options', __NAMESPACE__ . '\\filter_js_options' );
}

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
