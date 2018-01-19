<?php
/**
 * Get All Taxonomies
 *
 * @param none
 * @return array returns an array of taxonomy objects
 * @since 0.2.0
 */

function bre_get_taxonomies() {

  // set an empty array
  $bre_taxonomies = array();

  // get all taxonomies as objects
  $taxonomies = get_taxonomies(array(),'objects');

  // Loop through each taxonomy and get its data
  foreach($taxonomies as $tax){
    $bre_tax_obj = new stdClass();

  	$bre_tax_obj->name = $tax->label;
    $bre_tax_obj->slug = $tax->name;
    $bre_tax_obj->description = $tax->description;
    $bre_tax_obj->hierarchical = $tax->hierarchical;

    // push the data to the array
    array_push($bre_taxonomies, $bre_tax_obj);
  }

  /*
   *
   * Register Rest API Endpoint
   *
   */

  register_rest_route( 'better-rest-endpoints/v1', '/taxonomies/', array(
    'methods' => 'GET',
    'callback' => function ( WP_REST_Request $request ) use($bre_taxonomies) {
      return $bre_taxonomies;
    },
  ));
}

/*
 *
 * Add action for taxonomies
 *
 */
add_action( 'rest_api_init', 'bre_get_taxonomies' );
