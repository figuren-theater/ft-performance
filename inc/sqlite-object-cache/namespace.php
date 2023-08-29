<?php
/**
 * Figuren_Theater Performance Sqlite_Object_Cache.
 *
 * @package figuren-theater/performance/sqlite-object-cache
 */

namespace Figuren_Theater\Performance\Sqlite_Object_Cache;

use Figuren_Theater;
use Figuren_Theater\Options;
use function add_action;
use function current_user_can;
use function get_current_screen;
use function remove_submenu_page;
use function wp_dequeue_style;

const BASENAME = 'sqlite-object-cache/sqlite-object-cache.php';
const PLUGINPATH = WP_PLUGIN_DIR . '/' . BASENAME; // @TODO ugly hardcoded WP_CONTENT_DIR inside plugin, needs issue !!

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	// Remove 'Activate'-Link from Plugins on the Plugin-List
	// (1) is Plugin is not allowed or (2) if is PRODUCTION environment.
	add_action( 'plugin_action_links', __NAMESPACE__ . '\\remove_plugin_action_links', 10, 2 );
	add_action( 'network_admin_plugin_action_links', __NAMESPACE__ . '\\remove_plugin_action_links', 10, 4 );

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['sqlite-object-cache'] ) {
		return;
	}

	require_once PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\remove_scripts', 11 );
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {

	$_options = [
		'retention'          => '24',  // Cached data expires after n hours.
		// 'capture'         => 'on',  // Checked for existence, not for value; so commenting it out - is ok.
		'frequency'          => '100', // Relates to measuring the captures.
		'retainmeasurements' => '2',   // Relates to measuring the captures.
		// 'previouscapture' => 1673228570, // relates to measuring the captures, that's why this would need a merged option !
	];

	/*
	 * Gets added to the 'OptionsCollection'
	 * from within itself on creation.
	 */
	new Options\Option(
		'sqlite_object_cache_settings',
		$_options,
		BASENAME
	);
}

/**
 * Show the admin-menu, only:
 * - to super-administrators
 *
 * @return void
 */
function remove_menu() :void {

	if ( current_user_can( 'manage_sites' ) ) {
		return;
	}

	remove_submenu_page( 'options-general.php', 'sqlite_object_cache_settings' );
}

function remove_scripts() : void {

	if ( 'settings_page_sqlite_object_cache_settings' === get_current_screen()->id ) {
		return;
	}

		wp_dequeue_style( 'sqlite_object_cache-admin' );
}

/**
 *
 *
 * @param  [string[]] $links_array       An array of plugin action links. By default this can include 'activate', 'deactivate', and 'delete'. With Multisite active this can also include 'network_active' and 'network_only' items.
 * @param  [string]   $plugin_file_name  Path to the plugin file relative to the plugins directory.
 * @param  [Array]    $plugin_data       Contains all the plugin meta information, like Name, Description, Author, AuthorURI etc.
 * @param  [String]   $context           The plugin status. It can include by default: ‘all’, ‘active’, ‘recently_activated’, ‘inactive’, ‘upgrade’, ‘dropins’, ‘mustuse’, and ‘search’.
 *
 * @return [Array]    $links_array
 */
function remove_plugin_action_links( $links_array, $plugin_file_name, $plugin_data = null, $context = null ) {

	if ( BASENAME !== $plugin_file_name ) {
		return $links_array;
	}

	$links_array   = [];
	$links_array[] = '<span style="color:#888">autoloaded by <code>' . __NAMESPACE__ . '</code></span>';

	return $links_array;
}
