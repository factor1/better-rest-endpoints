<?php
/*
Plugin Name:  Better WordPress Endpoints
Plugin URI:   https://github.com/factor1/better-wp-endpoints/
Description:  Serves up slimmer WordPress Rest API endpoints.
Version:      0.0.1
Author:       Eric Stout, Factor1 Studios
Author URI:   https://factor1studios.com/
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Text Domain:  better-wp-endpoints
Domain Path:  /languages

Better WordPress Endpoints is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Better WordPress Endpoints is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Better WordPress Endpoints. If not, see {URI to Plugin License}.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// get a post by id
include_once 'inc/get_post_by_id.php';


function get_data( WP_REST_Request $request ) {
  $parameters = $request['id'];
  return $parameters;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'better-wp-endpoints/v1', '/data/', array(
    'methods' => 'GET',
    'callback' => 'get_data',
    'args' => array(
      'id' => array(
        'validate_callback' => function($param, $request, $key) {
          return is_numeric( $param );
        }
      ),
    ),
  ) );
} );
