<?php
/**
 * ft-performance
 *
 * @package           figuren-theater/performance
 * @author            figuren.theater
 * @copyright         2022 figuren.theater
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       figuren.theater | Performance
 * Plugin URI:        https://github.com/figuren-theater/ft-performance
 * Description:       Fast websites are more accessible, more sustainable and are giving a better UX. This is the code which accelerates figuren.theater and its WordPress Multisite Network.
 * Version:           1.2.2
 * Requires at least: 6.0
 * Requires PHP:      7.2
 * Author:            figuren.theater
 * Author URI:        https://figuren.theater
 * Text Domain:       figurentheater
 * Domain Path:       /languages
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Update URI:        https://github.com/figuren-theater/ft-performance
 */


namespace Figuren_Theater\Performance;

const DIRECTORY = __DIR__;

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
