<?php
/**
 * Figuren_Theater Performance Cache_Enabler.
 *
 * @package figuren-theater/ft-performance
 */

namespace Figuren_Theater\Performance\Cache_Enabler;

use Figuren_Theater;

use Figuren_Theater\Options;
use FT_VENDOR_DIR;
use function add_action;

use function add_filter;
use function is_admin;
use function remove_action;

const BASENAME   = 'cache-enabler/cache-enabler.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin(): void {

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['cache-enabler'] ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	if ( ! class_exists( 'Cache_Enabler' ) || ! is_admin() ) {
		return;
	}

	remove_action( 'admin_init', [ 'Cache_Enabler', 'register_settings' ] );
	remove_action( 'admin_menu', [ 'Cache_Enabler', 'add_settings_page' ] );
	remove_action( 'admin_enqueue_scripts', [ 'Cache_Enabler', 'add_admin_resources' ] );
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options(): void {

	$_options = [
		// @see https://www.keycdn.com/support/wordpress-cache-enabler-plugin#option !!!
		// stand vom 19.04.2022
		// stand vom 14.06.2022
		// stand vom 28.04.2023
		// 'version'                            => '1.8.7',
		'use_trailing_slashes'               => 1,
		'permalink_structure'                => 'has_trailing_slash',
		'cache_expires'                      => 0,
		'cache_expiry_time'                  => 0,
		'clear_site_cache_on_saved_post'     => 1,
		'clear_site_cache_on_saved_comment'  => 0,
		'clear_site_cache_on_saved_term'     => 1,
		'clear_site_cache_on_saved_user'     => 0,
		'clear_site_cache_on_changed_plugin' => 0,
		// Maybe re-enable when we ship .jpeg and .webp in parallel.
		'convert_image_urls_to_webp'         => '',
		'mobile_cache'                       => 0,
		'compress_cache'                     => 1,
		'minify_html'                        => 1,
		'minify_inline_css_js'               => 1,
		'excluded_post_ids'                  => '',
		// Ein regulärer Ausdruck, der Seitenpfaden entspricht, die den Zwischenspeicher umgehen sollen.
		'excluded_page_paths'                => '/^\/' . getenv( 'FT_SECURITY_LOGIN_SLUG' ) . '.*/', // Works!
		'excluded_query_strings'             => '',
		'excluded_cookies'                   => '/^(wp-postpass|wordpress_|comment_author)_/',
	];

	/*
	 * Gets added to the 'OptionsCollection'
	 * from within itself on creation.
	 */
	new Options\Option_Merged(
		'cache_enabler', // Was cache-enabler until 1.5.0 !
		$_options,
		BASENAME
	);

	// 'Cache Enabler' stopped using the normal options
	// and instead it uses *.php files inside of content/settings/...
	// So our normal options handling doesn't work.
	//
	// But this filter does.
	add_filter(
		'cache_enabler_settings_before_validation',
		function () use ( $_options ) {
			return $_options;
		}
	);

	add_filter(
		'cache_enabler_page_contents_before_store',
		function ( $html_to_save ) {
			// Get secret login slug.
			$login_slug = (string) getenv( 'FT_SECURITY_LOGIN_SLUG' );

			// Make sure our Login-URL never lands in cache
			// this also prevents the admin_bar of
			// dripping into the cache for whatever reason.
			$security_alert = (bool) strpos( $html_to_save, $login_slug );
			return ( $security_alert ) ? '' : $html_to_save;
		}
	);
}
