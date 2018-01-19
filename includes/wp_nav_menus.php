<?php
/**
 * Get WordPress Nav Menus
 *
 * @param N/A
 * @return object|null Return wp_nav_menu object,  * or null if none.
 * @since 0.1.1
 */

function bre_get_menus() {

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
      $items = new stdClass();

      $items->ID = $menu_item->ID;
      $items->menu_order = $menu_item->menu_order;
      $items->title = $menu_item->title;
      $items->slug = basename($menu_item->url);
      $items->url = $menu_item->url;
      $items->target = $menu_item->target;
      $items->description = $menu_item->description;
      $items->classes = $menu_item->classes;
      $items->menu_item_parent = $menu_item->menu_item_parent;

      array_push($slim_menu_items, $items);
    }

    /*
     *
     * Register Rest API Endpoint
     *
     */

      register_rest_route( 'better-rest-endpoints/v1', '/menus/'.$menu->slug.'/', array(
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
add_action( 'rest_api_init', 'bre_get_menus' );
