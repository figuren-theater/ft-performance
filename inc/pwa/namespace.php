<?php
/**
 * Figuren_Theater Performance PWA.
 *
 * @package figuren-theater/ft-performance
 */

namespace Figuren_Theater\Performance\PWA;

use Figuren_Theater;
use Figuren_Theater\Options;
use Figuren_Theater\Performance;
use Figuren_Theater\Theming\Themed_Login;
use FT_VENDOR_DIR;
use WPMU_PLUGIN_URL;
use WP_DEBUG;
use WP_Post;
use WP_Term;
use WP_Web_App_Manifest;
use function add_action;
use function add_filter;
use function add_query_arg;
use function esc_url;
use function get_option;
use function get_permalink;
use function get_post;
use function get_term;
use function get_term_link;
use function get_the_excerpt;
use function get_the_title;
use function home_url;
use function rawurlencode;

const BASENAME   = 'pwa/pwa.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin(): void {

	$config = Figuren_Theater\get_config()['modules']['performance'];
	if ( ! $config['pwa'] ) {
		return;
	}

	// Enable verbose Workbox.js logging in the browser console.
	define( 'WP_SERVICE_WORKER_DEBUG_LOG', WP_DEBUG ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_filter( 'web_app_manifest', __NAMESPACE__ . '\\modify_manifest' );

	add_filter( 'wp_resource_hints', __NAMESPACE__ . '\\prefetch_manifest', 10, 2 );

	// Filters service worker caching configuration for theme asset requests.
	add_filter( 'wp_service_worker_theme_asset_caching', __NAMESPACE__ . '\\theme_asset_caching' );
	add_filter( 'wp_service_worker_plugin_asset_caching', __NAMESPACE__ . '\\theme_asset_caching' );
	add_filter( 'wp_service_worker_core_asset_caching', __NAMESPACE__ . '\\theme_asset_caching' );

	/**
	 * Enable offline media.
	 *
	 * UNUSED, at the moment
	 *
	 * @todo #25 Re-Enable offline media.
	 *
	 * @see docblock of wp_front_service_worker__offline_media()
	 *
	 * add_filter( 'wp_front_service_worker', __NAMESPACE__ . '\\wp_front_service_worker__offline_media' );
	 */
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options(): void {

	$_options = [
		'offline_browsing' => 1,
	];

	/*
	 * Gets added to the 'OptionsCollection'
	 * from within itself on creation.
	 */
	new Options\Factory(
		$_options,
		'Figuren_Theater\Options\Option',
		BASENAME,
	);
}

/**
 * Add the web app manifest url to the list of 'prefetch'ed ressources.
 *
 * The used filter: Filters domains and URLs for resource hints of relation type.
 *
 * @param array<int, string|array<string, string>> $urls {
 *    Array of resources and their attributes, or URLs to print for resource hints.
 *
 *     @type array|string ...$0 {
 *         Array of resource attributes, or a URL string.
 *
 *         @type string $href        URL to include in resource hints. Required.
 *         @type string $as          How the browser should treat the resource
 *                                   (`script`, `style`, `image`, `document`, etc).
 *         @type string $crossorigin Indicates the CORS policy of the specified resource.
 *         @type float  $pr          Expected probability that the resource hint will be used.
 *         @type string $type        Type of the resource (`text/html`, `text/css`, etc).
 *     }
 * }
 * @param string                                   $relation_type The relation type the URLs are printed for,
 *                                                                e.g. 'preconnect' or 'prerender'.
 *
 * @return array<int, string|array<string, string>>
 */
function prefetch_manifest( array $urls, string $relation_type ): array {

	if ( ! class_exists( 'WP_Web_App_Manifest' ) ) {
		return $urls;
	}

	if ( 'prefetch' === $relation_type ) {
		$urls[] = WP_Web_App_Manifest::get_url();
	}

	return $urls;
}

/**
 * Overriding the (default) manifest json.
 *
 * There are more possible values for this, including 'orientation' and 'scope.'
 * See the documentation: https://developers.google.com/web/fundamentals/web-app-manifest/
 *
 * @param array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 *
 * @return array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 */
function modify_manifest( array $manifest ): array {

	$manifest = set_shortcuts( $manifest );
	$manifest = set_colors( $manifest );
	$manifest = set_defaults( $manifest );
	$manifest = set_screenshots( $manifest );

	return $manifest;
}

/**
 * Provide an app shortcut to the latest news via either the defined blog_posts page or the default_category.
 *
 * Overriding the (default) manifest json.
 *
 * @param array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 *
 * @return array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 */
function set_shortcuts( array $manifest ): array {

	$page_for_posts_id = get_option( 'page_for_posts' );
	if ( \is_int( $page_for_posts_id ) && ! empty( $page_for_posts_id ) ) {
		$page_for_posts = get_post( $page_for_posts_id );
		if ( $page_for_posts instanceof WP_Post ) {
			$name    = get_the_title( $page_for_posts );
			$url     = get_permalink( $page_for_posts );
			$excerpt = get_the_excerpt( $page_for_posts );
		}
	} else {
		// Try to use the default category for posts
		// as the base url for any kind of news.
		$default_category = get_term( (int) get_option( 'default_category' ), 'category' );
		if ( $default_category instanceof WP_Term ) {
			$name    = $default_category->name;
			$url     = get_term_link( $default_category );
			$excerpt = $default_category->description;
		}
	}

	if ( ! isset( $name ) || ! isset( $url ) ) {
		return $manifest;
	}

	if ( \is_string( $url ) && esc_url( $url ) ) {

		$excerpt = ( ! empty( $excerpt ) ) ? $excerpt : __( 'News', 'figurentheater' );

		$manifest['shortcuts'][] = [
			'name'        => $name,
			'url'         => $url,
			'description' => $excerpt,

			/**
			 * Icons 2 SVG 2 data-uri
			 *
			 * @see https://icon-sets.iconify.design/dashicons/admin-post/
			 */
			'icons'       => [
				[
					'src'     => WPMU_PLUGIN_URL . Performance\ASSETS_URL . 'svg/admin-post.svg',
					'type'    => 'image/svg+xml',
					'purpose' => 'any monochrome',
				],
			],
		];
	}

	return $manifest;
}

/**
 * Provide app colors for background and theme.
 *
 * Overriding the (default) manifest json.
 *
 * @todo #29 Remove spaghetti-dependency to f.t/ft-theming
 *
 * @param array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 *
 * @return array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 */
function set_colors( array $manifest ): array {
	$relevant_colors = Themed_Login\ft_get_relevant_colors();

	$manifest['background_color'] = $relevant_colors['ft_accent'];
	$manifest['theme_color']      = $relevant_colors['ft_accent'];

	return $manifest;
}

/**
 * Provide app defaults for OS integration and UI.
 *
 * Overriding the (default) manifest json.
 *
 * @param array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 *
 * @return array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 */
function set_defaults( array $manifest ): array {

	$manifest['orientation'] = 'any';
	$manifest['display']     = 'standalone';
	$manifest['url_handler'] = [
		'origin' => home_url( '/', 'https' ),
	];

	return $manifest;
}

/**
 * Provide an app screenshot of the home-page to be shown in the install dialog.
 *
 * Overriding the (default) manifest json.
 *
 * @param array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 *
 * @return array<string, mixed> $manifest Data of the manifest, to send in the REST API response.
 */
function set_screenshots( array $manifest ): array {

	if ( ! isset( $manifest['screenshots'] ) ) {
		$manifest['screenshots'] = [];
	}

	$manifest['screenshots'][] = [
		'src'   => get_shot( home_url(), 900, 2000 ),
		'sizes' => '900x2000',
		'type'  => 'image/jpeg',
	];

	return $manifest;
}

/**
 * Get Browser Screenshot
 *
 * Get a screenshot of a website using Automatics mShots service.
 *
 * @todo   #28  Re-Enable locale storage for PWA screenshots (privacy)
 *
 * @see    https://github.com/BinaryMoon/browser-shots/blob/master/browser-shots.php#L176-L202
 * @see    https://github.com/Automattic/mShots/blob/master/public_html/class-mshots.php
 *
 * @since  1.3 Removed call $this->save_screenshot( $remote_url, $new_name )
 *
 * @param  string $url      Url to screenshot.
 * @param  int    $width    Width of screenshot.
 * @param  int    $height   Height of screenshot.
 *
 * @return string
 */
function get_shot( string $url = '', int $width = 600, int $height = 450 ): string {

	// Image found.
	if ( '' !== $url ) {
		/**
		 * Possible indexes are: 'vpw', 'vph' and 'scale'.
		 *
		 * @param array<string, string> $args Service arguments of the wp.com/mshots API.
		 */
		$args = [
			'vpw' => (string) $width,
			'vph' => (string) $height,
		];

		$remote_url = 'https://s0.wp.com/mshots/v1/' . rawurlencode( esc_url( $url ) );
		/**
		 * Values of add_query_arg() are expected to be encoded
		 * appropriately with urlencode() or rawurlencode().
		 *
		 * Using rawurlencode on any variable used as part of the query string,
		 * either by using add_query_arg() or directly by string concatenation,
		 * will prevent parameter hijacking.
		 *
		 * @see  https://docs.wpvip.com/technical-references/code-quality-and-best-practices/encode-values-passed-to-add_query_arg/
		 */
		$args       = \array_map( 'rawurlencode', $args );
		$remote_url = add_query_arg( $args, $remote_url );

		return $remote_url;
	}

	return '';
}

/**
 * Filters service worker caching configuration for theme asset requests.
 *
 * @since PWA 0.6
 *
 * @param array<string, string|null|array<mixed>> $config {
 *     Theme asset caching configuration. If array filtered to be empty, then caching is disabled.
 *
 *     @type string     $route      Route. Regular expression pattern to match. See <https://developers.google.com/web/tools/workbox/reference-docs/latest/module-workbox-routing#.registerRoute>.
 *     @type string     $strategy   Strategy. Defaults to NetworkFirst.
 *                                  Even though assets should have far-future expiration, network-first is still preferred for development purposes.
 *                                  See <https://developers.google.com/web/tools/workbox/reference-docs/latest/module-workbox-strategies>.
 *     @type string     $cache_name Cache name. Defaults to 'uploaded-images'. This will get a site-specific prefix to prevent subdirectory multisite conflicts.
 *     @type array|null $expiration {
 *          Expiration plugin configuration. See <https://developers.google.com/web/tools/workbox/reference-docs/latest/module-workbox-expiration.ExpirationPlugin>.
 *
 *          @type int|null $max_entries     Max entries to cache. Defaults to 34.
 *                                          This limit the cached entries to the number of files loaded over network, e.g. JS, CSS, and PNG.
 *                                          The number 34 is derived from the 75th percentile of theme assets used on pages served from
 *                                          WordPress sites, as indexed by HTTP Archive.
 *                                          See https://github.com/GoogleChromeLabs/pwa-wp/issues/265#issuecomment-706612536.
 *          @type int|null $max_age_seconds Max age seconds. Defaults to null.
 *     }
 *     @type array|null $broadcast_update   Broadcast update plugin configuration. Not included by default. See <https://developers.google.com/web/tools/workbox/reference-docs/latest/module-workbox-broadcast-update.BroadcastUpdatePlugin>.
 *     @type array|null $cacheable_response Cacheable response plugin configuration. Not included by default. See <https://developers.google.com/web/tools/workbox/reference-docs/latest/module-workbox-cacheable-response.CacheableResponsePlugin>.
 * }
 *
 * @return array<string, string|null|array<mixed>>
 */
function theme_asset_caching( array $config ): array {
	// 'NetworkFirst' is the default.
	$config['strategy'] = \WP_Service_Worker_Caching_Routes::STRATEGY_STALE_WHILE_REVALIDATE;

	return $config;
}

/**
// TEMP DISABLED ::
	protected function save_screenshot( string $file_url, string $new_file_name ) : string
	{

		// If the function it's not available, require it.
		if ( ! function_exists( 'download_url' ) ) {
			require_once \ABSPATH . 'wp-admin/includes/file.php';
		}

		$dir = $this->set_screenshots_dir_path();


		// check is directory exists
		if ( !file_exists( $dir ) && !is_dir( $dir) )
			mkdir($dir);

		// Sets file final destination.
		$filepath = $dir . '/' . $new_file_name;

		// Now you can use it!
		// $file_url = 'https://example.com/myfile.ext';
		$tmp_file = \download_url( $file_url );

		// Copies the file to the final destination and deletes temporary file.
		copy( $tmp_file, $filepath );
 *
		@unlink( $tmp_file );

		$public_url = $this->set_screenshots_dir_url() . $new_file_name;
		return $public_url;
	}

	protected function set_screenshots_dir_path() : string
	{

		// VARIANT 1
		// save to cache folder
		#$current_domain = substr( \get_site_url(), 8);
		#$dir            = \ABSPATH . 'content/cache/'.$current_domain;
		#$public_url     = \WP_CONTENT_URL .'/cache/'.$current_domain;

		// VARIANT 2
		// save to __media
		// $this->set_screenshots_dir_url( '/__media/' );

		$blog_id    = \get_current_blog_id();
		return $this->screenshots_dir_path = \WP_CONTENT_DIR . '/uploads/sites/'.$blog_id;
	}

	protected function set_screenshots_dir_url() : string
	{
		return $this->screenshots_dir_url = \home_url( '/__media/', 'https' );
	}
__// TEMP DISABLED :: END
*/

	/**
	 * UNUSED at the moment
	 * because, it can't be that easy to have offline media
	 * as this repo and its readme descripe deeply:
	 * https://github.com/daffinm/audio-cache-test
	 *
	 * [wp_front_service_worker__offline_media description]
	 *
	 * @package figurentheater
	 * @version 2022.09.14
	 * @author  Carsten Bach
	 *
	 * @param   \WP_Service_Worker_Scripts $scripts [description]
	 * @return  void

	function wp_front_service_worker__offline_media( \WP_Service_Worker_Scripts $scripts )
	{
		$scripts->caching_routes()->register(
			'/__media/.*\.(?:png|gif|jpg|jpeg|svg|webp)(\?.*)?$',
			array(
				'strategy'  => \WP_Service_Worker_Caching_Routes::STRATEGY_NETWORK_FIRST,
				'cacheName' => 'ft_nw_images',
				'plugins'   => array(
					'expiration' => array(
						'maxEntries'    => 60,
						'maxAgeSeconds' => \MONTH_IN_SECONDS,
					),
				),
			)
		);
		$scripts->caching_routes()->register(
			'/__media/.*\.(?:mp3)(\?.*)?$',
			array(
				'strategy'  => \WP_Service_Worker_Caching_Routes::STRATEGY_NETWORK_FIRST,
				'cacheName' => 'ft_music',
				'plugins'   => array(
					'expiration' => array(
						'maxEntries'    => 60,
						'maxAgeSeconds' => \YEAR_IN_SECONDS,
					),
				),
			)
		);
	}
	 */
