<?php
/**
 * Figuren_Theater Performance Sqlite_Object_Cache.
 *
 * @package figuren-theater/performance/sqlite-object-cache
 */

namespace Figuren_Theater\Performance\Sqlite_Object_Cache;

use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;

const BASENAME   = 'sqlite-object-cache/sqlite-object-cache.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['sqlite-object-cache'] )
		return; // early

	require_once PLUGINPATH;

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\remove_scripts', 11 );

}

function remove_menu() : void {

	if ( current_user_can( 'manage_sites' ) )
		return;

	remove_submenu_page( 'options-general.php', 'sqlite_object_cache_settings' );
}

function remove_scripts() : void {
	
	if ('settings_page_sqlite_object_cache_settings' === get_current_screen()->id )
		return;

	// 
	wp_dequeue_style( 'sqlite_object_cache-admin' );
}
