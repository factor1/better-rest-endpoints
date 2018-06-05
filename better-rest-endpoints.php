<?php
/*
Plugin Name:  Better Rest Endpoints
Plugin URI:   https://github.com/factor1/better-rest-endpoints/
Description:  Serves up slimmer WordPress Rest API endpoints, with some great enhancements.
Version:      1.3.0
Author:       Eric Stout, Factor1 Studios
Author URI:   https://factor1studios.com/
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Text Domain:  better-rest-endpoints
Domain Path:  /languages

Better Rest Endpoints is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Better Rest Endpoints is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Better Rest Endpoints. If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class F1_Better_Rest_Endpoints {

	/**
	 * @var $instance - The One true copy of F1_Better_Rest_Endpoints that we'll ever need
	 */
	private static $instance;

	/**
	 * @var $plugin_dir - The plugin directory, for reuse in the includes.
	 */
	private static $plugin_dir;

	/**
	 * F1_Better_Rest_Endpoints constructor.
	 */
	private function __construct() {}

	/**
	 * Determines if we've already loaded the plugin, and if so returns it.
	 * Otherwise it kicks up a new instance of itself and stores it for later use.
	 *
	 * This is the singleton method.
	 *
	 * @return F1_Better_Rest_Endpoints
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof F1_Better_Rest_Endpoints ) ) {
			self::$instance = new F1_Better_Rest_Endpoints;

			self::$plugin_dir = trailingslashit( dirname( __FILE__ ) );

			// Load the includes
			self::$instance->includes();
		}

		return self::$instance;
	}

	/**
	 * Include the necessary files.
	 */
	private function includes() {
		// include acf function
		include_once self::$plugin_dir . 'includes/get_acf.php';

		// include yoast seo function
		include_once self::$plugin_dir . 'includes/get_yoast.php';

		// get a post by id
		include_once self::$plugin_dir . 'includes/get_post_by_id.php';

		// get a post by slug
		include_once self::$plugin_dir . 'includes/get_post_by_slug.php';

		// get posts
		include_once self::$plugin_dir . 'includes/get_posts.php';

		// get pages
		include_once self::$plugin_dir . 'includes/get_pages.php';

    // get page by id
		include_once self::$plugin_dir . 'includes/get_page_by_id.php';

    // get cpts
    include_once self::$plugin_dir . 'includes/get_cpts.php';

    // create custom post type endpoints
		include_once self::$plugin_dir . 'includes/create_cpt_endpoints.php';

    // get custom post type by id
		include_once self::$plugin_dir . 'includes/get_cpt_by_id.php';

		// get custom post type by slug
		include_once self::$plugin_dir . 'includes/get_cpt_by_slug.php';

    // get custom post type by id
		include_once self::$plugin_dir . 'includes/wp_nav_menus.php';

		// get custom taxonomies
		include_once self::$plugin_dir . 'includes/get_tax.php';

		// get custom taxonomies post endpoints
		include_once self::$plugin_dir . 'includes/get_posts_tax.php';

		// get search endpoint
		include_once self::$plugin_dir . 'includes/get_search.php';

		// get taxonomies endpoint
		include_once self::$plugin_dir . 'includes/get_taxonomies.php';
	}

}

/**
 * Returns the one true F1_Better_Rest_Endpoints.
 *
 * Loads on plugins_loaded.
 *
 * @return F1_Better_Rest_Endpoints
 */
function better_rest_endpoints() {
	return F1_Better_Rest_Endpoints::instance();
}
add_action( 'plugins_loaded', 'better_rest_endpoints', 99 );
