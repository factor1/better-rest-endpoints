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

      // better wordpress endpoint post object
      $bwe_page = new stdClass();

      $bwe_page->id = get_the_ID();
      $bwe_page->title = get_the_title();
      $bwe_page->slug = basename(get_permalink());

      /*
       *
       * return template name
       *
       */
      if( get_page_template() ){
        // strip file extension to return just the name of the template
        $template_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename(get_page_template()));

        $bwe_page->template = $template_name;

      } else {
        $bwe_page->template = 'default';
      }

      $bwe_page->content = get_the_content();


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
      return $bwe_page;

    }
  } else {

    // return empty page array if no page
    return $bwe_page;

  }

}

add_action( 'rest_api_init', function () {
  register_rest_route( 'better-wp-endpoints/v1', '/page/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_page_by_id',
  ) );
} );
