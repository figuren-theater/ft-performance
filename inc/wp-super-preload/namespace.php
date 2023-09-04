<?php
/**
 * Figuren_Theater Performance WP_Super_Preload.
 *
 * @package figuren-theater/ft-performance
 */

namespace Figuren_Theater\Performance\WP_Super_Preload;

use Figuren_Theater;

use Figuren_Theater\Options;
use FT_VENDOR_DIR;
use function add_action;
use function add_filter;
use function current_user_can;
use function get_post_types;
use function get_taxonomies;
use function is_network_admin;
use function is_user_admin;
use function remove_submenu_page;
use function restore_current_blog;
use function site_url;
use function switch_to_blog;
use function wp_clear_scheduled_hook;
use function wp_installing;
use function wp_next_scheduled;
use function wp_schedule_event;
use MINUTE_IN_SECONDS;
use WP_Super_Preload;

const BASENAME   = 'wp-super-preload/wp-super-preload.php';
const PLUGINPATH = '/carstingaxion/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	if ( is_network_admin() || is_user_admin() ) {
		return;
	}

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['wp-super-preload'] ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_filter( 'default_option_super_preload_updates', __NAMESPACE__ . '\\default_option_super_preload_updates' );

	// After all post_types are (normally) registered.
	add_action( 'cache_enabler_site_cache_cleared', __NAMESPACE__ . '\\on_site_cache_deletion', 10, 3 );

	add_action( 'ft_preload_site_cache', __NAMESPACE__ . '\\preload_on_site_cache_deletion', 10, 2 );

	// Fine tuning for curl requests.
	add_filter( 'wp-super-preload\curl_setopt', __NAMESPACE__ . '\\curl_setopt' );

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {
	/*
	 * Gets added to the 'OptionsCollection'
	 * from within itself on creation.
	 */
	$super_preload_settings = new Options\Option(
		'super_preload_settings',
		[],
		BASENAME,
	);
	$super_preload_settings->set_filter_callback( __NAMESPACE__ . '\\pre_option_super_preload_settings' );
}

/**
 * Get all options, that this plugin uses, prepared for 'pre_get_option'.
 *
 * @return array<string, bool|string|array<mixed>>
 */
function pre_option_super_preload_settings() : array {
	return specific_super_preload_settings( static_super_preload_settings() );
}

/**
 * Get static & fixed options, that this plugin uses, prepared for 'pre_get_option'.
 *
 * @access private
 *
 * @return array<string, bool|string|array<mixed>>
 */
function static_super_preload_settings() : array {
	return [
		// Basic settings.
		'sitemap_urls'         => '',    // textarea // will be prepared during 'enable'.
		'additional_contents'  => [      // checkbox.
			'front_pages'      => false, // DO NOT USE, this is better handled by the included sitemaps.
			'fixed_pages'      => false, // DO NOT USE, this is better handled by the included sitemaps.
			'categories'       => false, // DO NOT USE, this is better handled by the included sitemaps.
			'tags'             => false, // DO NOT USE, this is better handled by the included sitemaps.
			'authors'          => false, // will be prepared during 'enable'.
			'monthly_archives' => true,
			'yearly_archives'  => true,
		],
		'additional_pages'     => '',            // textarea.
		'max_pages'            => '1200',        // text.
		'user_agent'           => '',            // text.

		'synchronize_gc'       => 'disable',     // select.

		'preload_freq'         => 'hourly',     // select // can be one of: "disable|hourly|twicedaily|daily".

		'preload_hh'           => '00',          // text.
		'preload_mm'           => '13',          // text // Die wilde 13.

		// Advanced settings.
		'split_preload'        => true,          // checkbox.
		'requests_per_split'   => '100',         // text.
		'parallel_requests'    => '10',          // text in numbers.
		'interval_in_msec'     => '500',         // text in milliseconds.
		'timeout_per_fetch'    => '10',          // text in seconds.
		'preload_time_limit'   => '600',         // text in seconds.
		'initial_delay'        => '10',          // text in seconds.
	];
}

/**
 * Get dynamic parts of the options, that this plugin uses, prepared for 'pre_get_option'.
 *
 * @access private
 *
 * @param  array<string, bool|string|array<mixed>> $super_preload_settings Static default parts of 'super_preload_settings'.
 *
 * @return array<string, bool|string|array<mixed>>
 */
function specific_super_preload_settings( array $super_preload_settings ) : array {

	$_public_post_types = array_merge(
		get_post_types( [
			'public'   => true,
			'_builtin' => false,
		] ),
		[
			'page',
			'post',
		],
	);

	// The 'dt_subscription' PT is visible/reachable when WP_DEBUG is true.
	// Remove it.
	$_public_post_types = array_diff( $_public_post_types, [ 'dt_subscription' ] );

	$_public_taxonomies = array_merge(
		get_taxonomies( [
			'public'   => true,
			'_builtin' => false,
		] ),
		[
			'post_tag',
			'category',
		],
	);

	$_sitemap_urls_of_current_site = array_merge(
		get_sitemap_urls( $_public_post_types ),
		get_sitemap_urls( $_public_taxonomies ),
	);

	// @todo #27 Remove & avoid dependency hell
	$_has_multiple_authors = ( ! Figuren_Theater\FT::site()->has_feature( [ 'einsamer-wolf' ] ) ) ? true : false;

	// Update our options.
	$super_preload_settings['sitemap_urls'] = join( ' ', $_sitemap_urls_of_current_site );

	if ( ! \is_array( $super_preload_settings['additional_contents'] ) ) {
		$super_preload_settings['additional_contents'] = [];
	}
	$super_preload_settings['additional_contents']['authors'] = $_has_multiple_authors;

	return $super_preload_settings;
}

/**
 * Bare bone defaults of get_option('super_preload_settings').
 *
 * @return array<string, int|string>
 */
function default_option_super_preload_updates() : array {
	return [
		'timestamp'    => 0,
		'proc_time'    => 0,
		'next_preload' => 0,
	];
}

/**
 * Get a list of valid (yoast-like) sitemap.xml URLs for the given post_types & taxonomies.
 *
 * @access private
 *
 * @param  string[] $data_names List of post_types- and/or taxonomies-slugs.
 *
 * @return string[]
 */
function get_sitemap_urls( array $data_names ) : array {
	return array_map(
		function( string $data_name ) : string {
			return site_url( '/' . $data_name . '-sitemap.xml', 'https' );
		},
		$data_names
	);
}

/**
 * Fires after the site cache has been cleared.
 *
 * @since  1.6.0
 * @since  1.8.0  The `$cache_cleared_index` parameter was added.
 *
 * @param  string                $cleared_url          Full URL of the (page|site) cleared.
 * @param  int                   $cleared_site_id      ID of the (page|site) cleared.
 * @param  array<string, mixed>  $cache_cleared_index  Index of the cache cleared.
 *
 * @return void
 */
function on_site_cache_deletion( string $cleared_url, int $cleared_site_id, array $cache_cleared_index ) : void {

	switch_to_blog( $cleared_site_id ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.switch_to_blog_switch_to_blog

	shedule_preload_on_cache_deletion( $cleared_url, $cleared_site_id, 'site' );

	restore_current_blog();
}

/**
 * Shedule the next cache-preload if it is not already sheduled within the next minute.
 *
 * @access private
 *
 * @param  string $url             Full URL of the (page|site) cleared.
 * @param  int    $id              ID of the (page|site) cleared.
 * @param  string $preload_type    Can be either 'site' or 'page'.
 *
 * @return void
 */
function shedule_preload_on_cache_deletion( string $url, int $id, string $preload_type ) : void {

	if ( wp_installing() ) {
		return;
	}

	$preload_handle = "ft_preload_{$preload_type}_cache";

	$hook_args = [
		$url,
		$id,
	];
	$_wp_next_scheduled = wp_next_scheduled( $preload_handle, $hook_args );

	$_calc = ( $_wp_next_scheduled < MINUTE_IN_SECONDS );

	if ( $_calc && false !== $_wp_next_scheduled ) {
		return;
	}

	// By the settings of 'cache-enabler'
	// the whole site-cache gets deleted,
	// when any post is updated.
	//
	// Sow we can safely ignore all explicit page-handling
	// as it will be pre-loaded via sitemap.
	//
	// But we've to make sure
	// to not create any duplicate-cron-jobs.
	wp_clear_scheduled_hook( $preload_handle, $hook_args );

	$timestamp         = strtotime( '+130 seconds' ); // 30sec was sometimes ok, but I think sometimes the sitemaps are not prepared yet, which results in half-and-half-results
	$preload_frequency = static_super_preload_settings();

	if ( ! isset( $preload_frequency['preload_freq'] ) || ! \is_string( $preload_frequency['preload_freq'] ) ) {
		return;
	}

	wp_schedule_event(
		$timestamp,
		// 'twicedaily',
		$preload_frequency['preload_freq'],
		$preload_handle,
		$hook_args
	);
}

/**
 * If this is called as cron,
 * it gets now parameters added.
 * Thats why we give such useless defaults.
 *
 * @param  string       $cleared_url [description]
 * @param  int          $cleared_id  [description]
 *
 * @return void
 */
function preload_on_site_cache_deletion( string $cleared_url = '', int $cleared_id = 0 ) : void {

	// May not been set.
	if ( 0 < $cleared_id ) {
		switch_to_blog( $cleared_id ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.switch_to_blog_switch_to_blog
	}

	// Get Plugin path (to get the right instance).
	$path = FT_VENDOR_DIR . PLUGINPATH;

	// Get THE one instance from plugin class.
	$wp_super_preload = WP_Super_Preload::get_instance( $path );

	// Run over all sitemaps and curl all URLs.
	$wp_super_preload->exec_preload();

	// May not been set.
	if ( 0 < $cleared_id ) {
		restore_current_blog();
	}
}

/**
 * Filters the curl_setopt options for a "multiple threads request" by the plugin.
 *
 * @see https://www.php.net/manual/en/function.curl-setopt.php
 *
 * @param  array<int, mixed> $curl_opts The CURLOPT_XXX option to set.
 *
 * @return array<int, mixed>
 */
function curl_setopt( array $curl_opts ) : array {
	$curl_opts[ CURLOPT_ENCODING ]   = 'gzip';
	$curl_opts[ CURLOPT_HTTPHEADER ] = [ 'Accept: image/webp' ];

	return $curl_opts;
}

/**
 * Show the admin-menu, only:
 * - to super-administrators
 *
 * @return void
 */
function remove_menu() : void {
	if ( current_user_can( 'manage_sites' ) ) {
		return;
	}

	remove_submenu_page( 'options-general.php', 'wp-super-preload' );
}

