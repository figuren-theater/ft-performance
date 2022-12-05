<?php
/**
 * Figuren_Theater Performance Cache_Control.
 *
 * @package figuren-theater/performance/cache-control
 */

namespace Figuren_Theater\Performance\Cache_Control;

use FT_VENDOR_DIR;

use Figuren_Theater;
use Figuren_Theater\Options;
use function Figuren_Theater\get_config;

use function add_action;
use function remove_action;

const BASENAME   = 'cache-control/cache-control.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

const OPTION_PREFIX = 'cache_control_';

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['cache-control'] )
		return; // early

	require_once PLUGINPATH;

	remove_action( 'init', 'cache_control_add_admin_page' );

}

function filter_options() : void {

	// this is only for better devx
	$_options = [
		'front_page'     => [
			'id'         => 'front_page',
			'name'       => 'Front page',
			'max_age'    => 300,           //  5 min
			's_maxage'   => 0,
			'staleerror' => 0,
			'stalereval' => 0
		],
		'singles'        => [
			'id'         => 'singles',
			'name'       => 'Posts',
			'max_age'    => 600,           // 10 min
			's_maxage'   => 0,
			'mmulti'     => 1,             // enabled
			'staleerror' => 0,
			'stalereval' => 0
		],
		'pages'          => [
			'id'         => 'pages',
			'name'       => 'Pages',
			'max_age'    => 1200,          // 20 min
			's_maxage'   => 0,
			'staleerror' => 0,
			'stalereval' => 0
		],
		'home'           => [
			'id'         => 'home',
			'name'       => 'Main index',
			'max_age'    => 180,           //  3 min
			's_maxage'   => 0,
			'paged'      => 5,             //  5 sec
			'staleerror' => 0,
			'stalereval' => 0
		],
		'categories'     => [
			'id'         => 'categories',
			'name'       => 'Categories',
			'max_age'    => 900,           // 15 min
			's_maxage'   => 0,
			'paged'      => 8,             //  8 sec
			'staleerror' => 0,
			'stalereval' => 0
		],
		'tags'           => [
			'id'         => 'tags',
			'name'       => 'Tags',
			'max_age'    => 900,           // 15 min
			's_maxage'   => 0,
			'paged'      => 10,            //  8 sec
			'staleerror' => 0,
			'stalereval' => 0
		],
		'authors'        => [
			'id'         => 'authors',
			'name'       => 'Authors',
			'max_age'    => 1800,          // 30 min
			's_maxage'   => 0,
			'paged'      => 10,            // 10 sec
			'staleerror' => 0,
			'stalereval' => 0
		],
		'dates'          => [
			'id'         => 'dates',
			'name'       => 'Dated archives',
			'max_age'    => 10800,         // 3 hours
			's_maxage'   => 0,
			'staleerror' => 0,
			'stalereval' => 0
		],
		'feeds'          => [
			'id'         => 'feeds',
			'name'       => 'Feeds',
			'max_age'    => 5400,          // 1 hours 30 min
			's_maxage'   => 0,
			'staleerror' => 0,
			'stalereval' => 0
		],
		'attachment'     => [
			'id'         => 'attachment',
			'name'       => 'Attachment pages',
			'max_age'    => 10800,         // 3 hours
			's_maxage'   => 0,
			'staleerror' => 0,
			'stalereval' => 0
		],
		'search'         => [
			'id'         => 'search',
			'name'       => 'Search results',
			'max_age'    => 1800,          // 30 min
			's_maxage'   => 0,
			'staleerror' => 0,
			'stalereval' => 0
		],
		'notfound'       => [
			'id'         => 'notfound',
			'name'       => '404 Not Found',
			'max_age'    => 900,           // 15 min
			's_maxage'   => 0,
			'staleerror' => 0,
			'stalereval' => 0
		],
		//'redirect_temporary' => [
		//    'id'         => 'redirect_temporary',
		//    'name'       => 'Temporary redirects',
		//    'max_age'    => 60,            // 60 sec
		//    's_maxage'   => 0,
		//    'staleerror' => 0,
		//    'stalereval' => 0
		//],
		'redirect_permanent' => [
			'id'         => 'redirect_permanent',
			'name'       => 'Permanent redirects',
			'max_age'    => 86400,         // 1 day
			's_maxage'   => 0,
			'staleerror' => 0,
			'stalereval' => 0
		]
	];

	// needed for saving
	$_separated_options = [];

	foreach ($_options as $content_type => $options) {
		
		// prepare option-name 1st part
		// this will be: 'cache_control_' . 'singles' . '_'
		$_option_name = OPTION_NAME_PREFIX . $content_type . '_';
		
		//
		foreach ($options as $option_type => $option_value) {
			
			if ( in_array($option_type, ['id','name'] ) )
				continue;

			// prepare option-name 2nd part
			$option_name = $_option_name . $option_type;
			//
			$_separated_options[ $option_name ] = $option_value;
		}
	}

	// see the tree ;)
	// die('<pre>'.var_export($separated_options,true).'</pre>');

	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new Options\Factory( 
		$_separated_options, 
		'Figuren_Theater\Options\Option', 
		BASENAME, 
	);
}
