<?php
/**
 * Get Posts by Taxonomy
 *
 * @param none
 * @return endpoint returns a WordPress Rest API Endpoint
 * @since 0.1.8
 */

function bre_build_custom_tax_endpoint() {
  if( bre_get_custom_tax() ){

    // store the custom tax collections we have
    $custom_tax_collection = bre_get_custom_tax();

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
        register_rest_route( 'better-rest-endpoints/v1', '/'.$tax.'/'.$tax_term->slug.'/', array(
          'methods' => 'GET',
          'callback' => function ( WP_REST_Request $request ) use ($tax, $tax_term) {

            // check for params
            $posts_per_page = $request['per_page']?: '10';
            $page = $request['page']?: '1';
            $content = $request['content'];
            $show_content = filter_var($content, FILTER_VALIDATE_BOOLEAN);
            $acf = $request['acf'];
            $show_acf = filter_var($acf, FILTER_VALIDATE_BOOLEAN);
            $yoast = $request['yoast'];
            $show_yoast = filter_var($yoast, FILTER_VALIDATE_BOOLEAN);
            $media = $request['media'];
            $show_media = filter_var($media, FILTER_VALIDATE_BOOLEAN);
            $orderby = $request['orderby']?: null;
            $order = $request['order']?: null;
            $exclude = $request['exclude']?: null;

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
                'order'                  => $order?:'DESC',
                'orderby'                => $orderby?:'date',
                'post__not_in'           => array($exclude)
              );

              // The Query
              $query = new WP_Query( $args );

              if( $query->have_posts() ){

                // For Headers
                $total = $query->found_posts;
                $pages = $query->max_num_pages;

                // setup post object
                $bre_tax_posts = array();

                while( $query->have_posts() ) {
                  $query->the_post();

                  global $post;

                  $bre_tax_post = new stdClass();

                  // get post data
                  $permalink = get_permalink();
                  $bre_tax_post->id = get_the_ID();
                  $bre_tax_post->title = get_the_title();
                  $bre_tax_post->slug = $post->post_name;
                  $bre_tax_post->permalink = $permalink;
                  $bre_tax_post->date = get_the_date('c');
                  $bre_tax_post->date_modified = get_the_modified_date('c');
                  $bre_tax_post->excerpt = get_the_excerpt();

                  if( $content === null || $show_content === true ){
                    $bre_tax_post->content = apply_filters('the_content', get_the_content());
                  }

                  $bre_tax_post->author = esc_html__(get_the_author(), 'text_domain');
                  $bre_tax_post->author_id = get_the_author_meta('ID');
                  $bre_tax_post->author_nicename = get_the_author_meta('user_nicename');

                  /*
                   *
                   * get the terms
                   *
                   */
                  if( get_the_terms(get_the_ID(), $tax) ){

                    $bre_tax_post->terms = get_the_terms(get_the_ID(), $tax);

                  } else {
                    $bre_tax_post->terms = array();
                  }


                  /*
                   *
                   * return acf fields if they exist
                   *
                   */
                   if( $acf === null || $show_acf === true ) {
                     $bre_tax_post->acf = bre_get_acf();
                   }

                  /*
                   *
                   * return Yoast SEO fields if they exist
                   *
                   */
                   if( $yoast === null || $show_yoast === true ) {
                     $bre_tax_post->yoast = bre_get_yoast( $bre_tax_post->id );
                   }

                   /*
                    *
                    * get possible thumbnail sizes and urls if query set to true or by default
                    *
                    */

                   if( $media === null || $show_media === true ) {
                     $thumbnail_names = get_intermediate_image_sizes();
                     $bre_thumbnails = new stdClass();

                     if( has_post_thumbnail() ){
                       foreach ($thumbnail_names as $key => $name) {
                         $bre_thumbnails->$name = esc_url(get_the_post_thumbnail_url($post->ID, $name));
                       }

                       $bre_tax_post->media = $bre_thumbnails;
                     } else {
                       $bre_tax_post->media = false;
                     }
                   }

                  // push the post to the main array
                  array_push($bre_tax_posts, $bre_tax_post);

                }
                // return the post array
                $response = rest_ensure_response( $bre_tax_posts );
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
              'acf' =>  array(
                'description'       => 'Hide or show acf fields from the collection.',
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
              'yoast' =>  array(
                'description'       => 'Hide or show Yoast SEO fields from the collection.',
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
              'media' =>  array(
                'description'       => 'Hide or show featured media from the collection.',
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
              'order' =>  array(
                'description'       => 'Change order of the collection.',
                'type'              => 'string',
                'validate_callback' => function($param, $request, $key) {
                    return is_string( $param );
                  },
                'sanitize_callback' => 'sanitize_text_field',
              ),
              'orderby' =>  array(
                'description'       => 'The sort order of the collection.',
                'type'              => 'string',
                'validate_callback' => function($param, $request, $key) {
                    return is_string( $param );
                  },
                'sanitize_callback' => 'sanitize_text_field'
              ),
              'exclude' =>  array(
                'description'       => 'Exclude a post by ID.',
                'type'              => 'integer',
                'validate_callback' => function( $param, $request, $key ) {
                  return is_numeric( $param );
                 },
                'sanitize_callback' => 'absint'
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
add_action( 'rest_api_init', 'bre_build_custom_tax_endpoint' );
