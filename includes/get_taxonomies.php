<?php
/**
 * Get All Taxonomies
 *
 * @param none
 * @return array returns an array of taxonomy objects
 * @since 0.2.0
 */

function bwe_get_taxonomies() {

  // set an empty array
  $bwe_taxonomies = array();

  // get all taxonomies as objects
  $taxonomies = get_taxonomies(array(),'objects');

  // Loop through each taxonomy and get its data
  foreach($taxonomies as $tax){
    $bwe_tax_obj = new stdClass();

  	$bwe_tax_obj->name = $tax->label;
    $bwe_tax_obj->slug = $tax->name;
    $bwe_tax_obj->description = $tax->description;
    $bwe_tax_obj->hierarchical = $tax->hierarchical;

    // push the data to the array
    array_push($bwe_taxonomies, $bwe_tax_obj);
  }

  /*
   *
   * Register Rest API Endpoint
   *
   */

  register_rest_route( 'better-wp-endpoints/v1', '/taxonomies/', array(
    'methods' => 'GET',
    'callback' => function ( WP_REST_Request $request ) use($bwe_taxonomies) {
      return $bwe_taxonomies;
    },
  ));
}

/*
 *
 * Add action for taxonomies
 *
 */
add_action( 'rest_api_init', 'bwe_get_taxonomies' );
