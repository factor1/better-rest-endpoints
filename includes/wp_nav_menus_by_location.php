<?php
/**
 * Get WordPress Nav Menus by Location
 *
 * @param N/A
 * @return object|null Return wp_nav_menu object,  * or null if none.
 * @since 0.1.1
 */

function get_menus_by_location( WP_REST_Request $request ) {
    $location = $request['location'];

    $theme_locations = get_nav_menu_locations();

    if( !array_key_exists($location, $theme_locations) ){
      return [];
    }

    $menu = wp_get_nav_menu_items($theme_locations[$location]);

    return array_map(function($row){
        $items = new stdClass();
        $items->ID = $row->ID;
        $items->menu_order = $row->menu_order;
        $items->title = $row->title;
        $items->slug = basename($row->url);
        $items->url = $row->url;
        $items->target = $row->target;
        $items->description = $row->description;
        $items->classes = $row->classes;
        $items->menu_item_parent = $row->menu_item_parent;

        return $items;
    }, $menu);
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'better-rest-endpoints/v1', '/menus/location/(?P<location>\S+)', array(
    'methods' => 'GET',
    'callback' => 'get_menus_by_location',
  ) );
} );
