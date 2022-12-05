<?php
/**
 * Figuren_Theater Performance.
 *
 * @package figuren-theater/performance
 */

namespace Figuren_Theater\Performance;

use WP_CACHE;

use Altis;
use function Altis\register_module;


/**
 * Register module.
 */
function register() {

	$default_settings = [
		// needs to be set
		'enabled'          => defined('WP_CACHE') && constant('WP_CACHE'),
		'cache-control'    => true,
		'cache-enabler'    => true,
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
	PWA\bootstrap();
	Quicklink\bootstrap();
	// WP_Super_Preload\bootstrap();
	
	// Best practices
	//...\bootstrap();
}
