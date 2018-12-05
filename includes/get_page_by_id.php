<?php
/**
 * Grab a page by ID
 *
 * @param array $data Options for the function.
 * @return array|null Single page data,  * or null if none.
 * @since 0.0.1
 */

function get_page_by_id( WP_REST_Request $request ){

  // query arguments
  $args = array(
    'page_id'   => $request['id']
  );

  // The Query
  $query = new WP_Query( $args );

  // The Loop
  if( $query->have_posts() ){
    while( $query->have_posts() ) {
      $query->the_post();
      global $post;

      // better wordpress endpoint post object
      $bre_page = new stdClass();

      $permalink = get_permalink();
      $bre_page->id = get_the_ID();
      $bre_page->title = get_the_title();
      $bre_page->slug = $post->post_name;
      $bre_page->permalink = $permalink;

      /*
       *
       * return template name
       *
       */
      if( get_page_template() ){
        // strip file extension to return just the name of the template
        $template_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename(get_page_template()));

        $bre_page->template = $template_name;

      } else {
        $bre_page->template = 'default';
      }

      $bre_page->content = apply_filters('the_content', get_the_content());

      /*
       *
       * return parent slug if it exists
       *
       */
      $parents = get_post_ancestors( $post->ID );
      /* Get the top Level page->ID count base 1, array base 0 so -1 */
    	$id = ($parents) ? $parents[count($parents)-1]: $post->ID;
    	/* Get the parent and set the $class with the page slug (post_name) */
      $parent = get_post( $id );
    	$bre_page->parent = $parent->post_name != $post->post_name ? $parent->post_name : false;

      /*
       *
       * return acf fields if they exist
       *
       */
      $bre_page->acf = bre_get_acf();

      /*
       *
       * return Yoast SEO fields if they exist
       *
       */
      $bre_page->yoast = bre_get_yoast( $bre_page->id );

      /*
       *
       * get possible thumbnail sizes and urls
       *
       */
      $thumbnail_names = get_intermediate_image_sizes();
      $bre_thumbnails = new stdClass();

      if( has_post_thumbnail() ){
        foreach ($thumbnail_names as $key => $name) {
          $bre_thumbnails->$name = esc_url(get_the_post_thumbnail_url($post->ID, $name));
        }

        $bre_page->media = $bre_thumbnails;
      } else {
        $bre_page->media = false;
      }

      // Push the post to the main $post array
      return $bre_page;

    }
  } else {

    // return empty page array if no page
    return [];

  }

}

add_action( 'rest_api_init', function () {
  register_rest_route( 'better-rest-endpoints/v1', '/page/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_page_by_id',
  ) );
} );
