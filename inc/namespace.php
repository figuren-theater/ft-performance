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

	$use_mem_cache = file_exists( WP_CONTENT_DIR . '/object-cache.php' );

	$default_settings = [
		// needs to be set
		'enabled'          => defined('WP_CACHE') && constant('WP_CACHE'),
		'cache-control'    => true,
		'cache-enabler'    => true,
		'native-gettext'   => ! $use_mem_cache,
		// 'dynamo'          => $use_mem_cache, // OR
		// 'fast-translate'  => $use_mem_cache,
		'pwa'              => false,
		'quicklink'        => true,
#		'wp-super-preload' => true,

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
	Native_Gettext\bootstrap();
	PWA\bootstrap();
	Quicklink\bootstrap();
	// WP_Super_Preload\bootstrap();
	
	// Best practices
	//...\bootstrap();
}
