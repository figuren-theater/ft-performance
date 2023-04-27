<?php
/**
 * Figuren_Theater Performance.
 *
 * @package figuren-theater/performance
 */

namespace Figuren_Theater\Performance;

use WP_CACHE;
use WP_CONTENT_DIR;

use Altis;
use function Altis\register_module;


/**
 * Register module.
 */
function register() {

	$use_cache     = defined( 'WP_CACHE' ) && constant( 'WP_CACHE' );
	// $use_mem_cache = file_exists( WP_CONTENT_DIR . '/object-cache.php' );

	$default_settings = [
		// needs to be set
		'enabled'             => true,

		'cache-control'       => $use_cache,
		'cache-enabler'       => $use_cache,

		// 'native-gettext'      => ! $use_mem_cache,
		'native-gettext'      => true,
		// 'dynamo'            => $use_mem_cache, // OR
		// 'fast-translate'    => $use_mem_cache, // OR
		// "A faster load_textdomain" --> https://gist.github.com/soderlind/610a9b24dbf95a678c3e
		
		'pwa'                 => false,
		'quicklink'           => $use_cache,
		
		'sqlite-object-cache' => $use_cache, // This file will be installed from this plugin, no chance to check against $use_mem_cache from here.
		'wp-super-preload'    => $use_cache,
	];
	$options = [
		'defaults' => $default_settings,
	];
	Altis\register_module(
		'performance',
		DIRECTORY,
		'Performance',
		$options,
		__NAMESPACE__ . '\\bootstrap'
	);
}

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	// Plugins
	Cache_Control\bootstrap();
	Cache_Enabler\bootstrap();
	Fast404\bootstrap();
	Native_Gettext\bootstrap();
	PWA\bootstrap();
	Quicklink\bootstrap();
	Sqlite_Object_Cache\bootstrap();
	WP_Super_Preload\bootstrap();
	
	// Best practices
	//...\bootstrap();
}
