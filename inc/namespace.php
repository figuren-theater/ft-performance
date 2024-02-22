<?php
/**
 * Figuren_Theater Performance.
 *
 * @package figuren-theater/ft-performance
 */

namespace Figuren_Theater\Performance;

use Altis;

const ASSETS_URL = '/FT/ft-performance/assets/';

/**
 * Register module.
 *
 * @return void
 */
function register(): void {

	$use_cache = defined( 'WP_CACHE' ) ? constant( 'WP_CACHE' ) : false;

	$default_settings = [
		'enabled'             => true,  // Needs to be set.

		'cache-control'       => $use_cache,
		'cache-enabler'       => $use_cache,
		'pwa'                 => false,
		'quicklink'           => $use_cache,
		'sqlite-object-cache' => $use_cache, // This file will be installed from this plugin, no chance to check against $use_mem_cache from here.
		'wp-super-preload'    => $use_cache,
	];
	$options          = [
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
 *
 * @return void
 */
function bootstrap(): void {

	// Plugins.
	Cache_Control\bootstrap();
	Cache_Enabler\bootstrap();
	Fast404\bootstrap();
	Performant_Translations\bootstrap();
	PWA\bootstrap();
	Quicklink\bootstrap();
	Sqlite_Object_Cache\bootstrap();
	WP_Super_Preload\bootstrap();
}
