<?php
/**
 * Grab a collection of pages
 *
 * @param array $data Options for the function.
 * @return array|null Collection of pages in an array,  * or null if none.
 * @since 0.0.1
 */

include 'get_acf.php';

// get a collection of pages with parameters
function bwe_get_pages( WP_REST_Request $request ) {

  // check for params
  $posts_per_page = $request['per_page']?: '10';
  $page = $request['page']?: '1';
  $show_content = $request['content']?: 'true';
  $orderby = $request['orderby']?: null;
  $order = $request['order']?: null;
  $exclude = $request['exclude']?: null;

  // WP_Query arguments
  $args = array(
    'post_type'              => 'page',
    'nopaging'               => false,
  	'posts_per_page'         => $posts_per_page,
    'paged'                  => $page,
    'order'                  => $order?:'DESC',
    'orderby'                => $orderby?:'date'
  );

  $query = new WP_Query( $args );

  // Setup pages array
  $pages = array();

  // The Loop
  if( $query->have_posts() ){
    while( $query->have_posts() ) {
      $query->the_post();

      // better wordpress endpoint page object
      $bwe_page = new stdClass();

      /*
       *
       * get page data
       *
       */
      $bwe_page->id = get_the_ID();
      $bwe_page->title = get_the_title();

      // show post content unless parameter is false
      if( $show_content === 'true' ) {
        $bwe_page->content = get_the_content();
      }

      /*
       *
       * return acf fields if they exist
       *
       */
      $bwe_page->acf = bwe_get_acf();

      /*
       *
       * get possible thumbnail sizes and urls
       *
       */
      $thumbnail_names = get_intermediate_image_sizes();
      $bwe_thumbnails = new stdClass();

      if( has_post_thumbnail() ){
        foreach ($thumbnail_names as $key => $name) {
          $bwe_thumbnails->$name = esc_url(get_the_post_thumbnail_url($post->ID, $name));
        }

        $bwe_page->media = $bwe_thumbnails;
      } else {
        $bwe_page->media = false;
      }

      // Push the post to the main $post array
      array_push($pages, $bwe_page);
    }

    // return the pages array
    return $pages;
  } else {

    // return the empty pages array if no posts
    return $pages;
  }

  // restore post data
  wp_reset_postdata();
}

 /*
  *
  * Register Rest API Endpoint
  *
  */
 add_action( 'rest_api_init', function () {
   register_rest_route( 'better-wp-endpoints/v1', '/posts/', array(
     'methods' => 'GET',
     'callback' => 'bwe_get_posts',
     'args' => array(
       'per_page' => array(
         'validate_callback' => 'is_numeric'
       ),
       'page' =>  array(
         'validate_callback' => 'is_numeric'
       ),
       'exclude' =>  array(
         'validate_callback' => 'is_numeric'
       ),
       'order' =>  array(
         'validate_callback' => 'is_numeric' // change to string
       ),
       'orderby' =>  array(
         'validate_callback' => 'is_numeric' // change to string
       ),
     ),
   ) );
 } );
