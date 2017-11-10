<?php
/**
 * Get Posts by Taxonomy
 *
 * @param none
 * @return endpoint returns a WordPress Rest API Endpoint
 * @since 0.1.8
 */

function bwe_build_custom_tax_endpoint() {
  if( bwe_get_custom_tax() ){

    // store the custom tax collections we have
    $custom_tax_collection = bwe_get_custom_tax();

    //print_r($custom_tax_collection);

    foreach ($custom_tax_collection as $key => $tax) {

      $tax_terms = get_terms(array(
        'taxonomy' => $tax,
        'hide_empty' => true
      ));

      foreach ($tax_terms as $key => $tax_term) {
        /*
         *
         * Register Rest API Endpoint
         *
         */
        register_rest_route( 'better-wp-endpoints/v1', '/'.$tax.'/'.$tax_term->slug.'/', array(
          'methods' => 'GET',
          'callback' => function ( WP_REST_Request $request ) use ($tax, $tax_term) {

            // check for params
            $posts_per_page = $request['per_page']?: '10';
            $page = $request['page']?: '1';
            $show_content = $request['content']?: 'true';
            $orderby = $request['orderby']? : null;

              // WP_Query Arguments
              $args = array(
                'nopaging'               => false,
              	'posts_per_page'         => $posts_per_page,
                'paged'                  => $page,
                'tax_query' => array(
                  array(
                    'taxonomy' => $tax,
                    'terms'    => $tax_term,
                    'field'    => 'slug'
                  )
                ),
                'orderby'                => $orderby
              );

              // The Query
              $query = new WP_Query( $args );

              if( $query->have_posts() ){

                // For Headers
                $total = $query->found_posts;
                $pages = $query->max_num_pages;

                // setup post object
                $bwe_tax_posts = array();

                while( $query->have_posts() ) {
                  $query->the_post();

                  $bwe_tax_post = new stdClass();

                  // get post data
                  $bwe_tax_post->id = get_the_ID();
                  $bwe_tax_post->title = get_the_title();
                  $bwe_tax_post->slug = basename(get_permalink());
                  $bwe_tax_post->date = get_the_date('c');
                  $bwe_tax_post->excerpt = get_the_excerpt();

                  if( $show_content ){
                    $bwe_tax_post->content = apply_filters('the_content', get_the_content());
                  }

                  $bwe_tax_post->author = esc_html__(get_the_author(), 'text_domain');
                  $bwe_tax_post->author_id = get_the_author_meta('ID');

                  /*
                   *
                   * get the terms
                   *
                   */
                  if( get_the_terms(get_the_ID(), $tax) ){

                    $bwe_tax_post->terms = get_the_terms(get_the_ID(), $tax);

                  } else {
                    $bwe_tax_post->terms = array();
                  }


                  /*
                   *
                   * return acf fields if they exist
                   *
                   */
                  $bwe_tax_post->acf = bwe_get_acf();

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

                    $bwe_tax_post->media = $bwe_thumbnails;
                  } else {
                    $bwe_tax_post->media = false;
                  }

                  // push the post to the main array
                  array_push($bwe_tax_posts, $bwe_tax_post);

                }
                // return the post array
                $response = rest_ensure_response( $bwe_tax_posts );
                $response->header( 'X-WP-Total', (int) $total );
                $response->header( 'X-WP-TotalPages', (int) $pages );

                return $response;

              } else {
                // if no post is found
                return array();
              }

              // reset post data
              wp_reset_postdata();

            },
            'args' => array(
              'per_page' => array(
                'description'       => 'Maxiumum number of items to show per page.',
                'type'              => 'integer',
                'validate_callback' => function( $param, $request, $key ) {
                  return is_numeric( $param );
                 },
                'sanitize_callback' => 'absint',
              ),
              'page' =>  array(
                'description'       => 'Current page of the collection.',
                'type'              => 'integer',
                'validate_callback' => function( $param, $request, $key ) {
                  return is_numeric( $param );
                 },
                'sanitize_callback' => 'absint'
              ),
              'content' =>  array(
                'description'       => 'Hide or show the_content from the collection.',
                'type'              => 'boolean',
                'validate_callback' => function( $param, $request, $key ) {

                  if ( $param == 'true' || $param == 'TRUE' ) {
                    $param = true;
                  } else if( $param == 'false' || $param == 'FALSE') {
                    $param = false;
                  }

                  return is_bool( $param );
                 }
              ),
              'orderby' =>  array(
                'description'       => 'The sort order of the collection.',
                'type'              => 'string',
                'validate_callback' => function($param, $request, $key) {
                    return is_string( $param );
                  },
                'sanitize_callback' => 'sanitize_text_field'
              ),
            ),
        ));
      }

    }
  } else {
    return array();
  }
}

/*
 *
 * Add action for custom tax endpoint building
 *
 */
add_action( 'rest_api_init', 'bwe_build_custom_tax_endpoint' );
