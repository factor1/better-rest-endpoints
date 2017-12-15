<?php
/**
 * Get WordPress Nav Menus
 *
 * @param N/A
 * @return object|null Return wp_nav_menu object,  * or null if none.
 * @since 0.1.1
 */

function bwe_get_menus() {

  // function to return menu array
  function wp_get_menu_array($current_menu) {

    $array_menu = wp_get_nav_menu_items($current_menu);
    $menu = array();
    foreach ($array_menu as $m) {
      if (empty($m->menu_item_parent)) {
          $menu[$m->ID] = array();
          $menu[$m->ID]['ID']          =   $m->ID;
          $menu[$m->ID]['title']       =   $m->title;
          $menu[$m->ID]['url']         =   $m->url;
          $menu[$m->ID]['slug']         =   basename($m->url);
          $menu[$m->ID]['children']    =   array();
      }
    }
    $submenu = array();
    foreach ($array_menu as $m) {
      if ($m->menu_item_parent) {
          $submenu[$m->ID] = array();
          $submenu[$m->ID]['ID']       =   $m->ID;
          $submenu[$m->ID]['title']    =   $m->title;
          $submenu[$m->ID]['url']      =   $m->url;
          $submenu[$m->ID]['slug']      =  basename($m->url);
          $menu[$m->menu_item_parent]['children'][$m->ID] = $submenu[$m->ID];
      }
    }
    return $menu;
  }

  // get registered menus
  $menus = get_terms('nav_menu');

  foreach ($menus as $key => $menu) {

    /*
     *
     * Filter out unwanted responses
     *
     */

    $menu_items = wp_get_menu_array($menu);



    /*
     *
     * Register Rest API Endpoint
     *
     */

      register_rest_route( 'better-wp-endpoints/v1', '/menus/'.$menu->slug.'/', array(
        'methods' => 'GET',
        'callback' => function ( WP_REST_Request $request ) use($menu_items) {
          return $menu_items;
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
