<?php
/**
 * Get WordPress Nav Menus
 *
 * @param N/A
 * @return object|null Return wp_nav_menu object,  * or null if none.
 * @since 0.1.1
 */

function bwe_get_menus() {

  // get registered menus
  $menus = get_terms('nav_menu');

  foreach ($menus as $key => $menu) {

    /*
     *
     * Filter out unwanted responses
     *
     */

    $menu_items = wp_get_nav_menu_items($menu->slug);

    $slim_menu_items = array();

    foreach ($menu_items as $key => $menu_item) {
      // create a new object for a smaller response 
      $items = new stdClass();

      // get menu item data and add it to our new object
      $items->menu_order = $menu_item->menu_order;
      $items->title = $menu_item->title;
      $items->url = $menu_item->url;
      $items->target = $menu_item->target;
      $items->description = $menu_item->description;
      $items->classes = $menu_item->classes;

      array_push($slim_menu_items, $items);
    }

    /*
     *
     * Register Rest API Endpoint
     *
     */

      register_rest_route( 'better-wp-endpoints/v1', '/menus/'.$menu->slug.'/', array(
        'methods' => 'GET',
        'callback' => function ( WP_REST_Request $request ) use($slim_menu_items) {
          return $slim_menu_items;
        },
      ));

  }

}

/*
 *
 * Add action for cpt endpoint building
 *
 */
add_action( 'rest_api_init', 'bwe_get_menus' );
