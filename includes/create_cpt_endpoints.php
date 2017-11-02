<?php
/**
 * Create Custom Post Type Endpoints
 *
 * @since 0.0.1
 */

function bwe_build_cpt_endpoints() {
  if( bwe_get_cpts() ){

    // store what custom post types we have
    $cpt_collection = bwe_get_cpts();

    foreach ($cpt_collection as $key => $cpt) {

      /*
       *
       * Register Rest API Endpoint
       *
       */

        register_rest_route( 'better-wp-endpoints/v1', '/'.$cpt.'/', array(
          'methods' => 'GET',
          'callback' => function ( WP_REST_Request $request ) use($cpt) {

            // check for params
            $posts_per_page = $request['per_page']?: '10';
            $page = $request['page']?: '1';
            $show_content = $request['content']?: 'true';
            $orderby = $request['orderby']? : null;

            // WP_Query arguments
            $args = array(
              'post_type'              => $cpt,
            	'nopaging'               => false,
            	'posts_per_page'         => $posts_per_page,
              'paged'                  => $page,
              'orderby'                => $orderby
            );

            // The Query
            $query = new WP_Query( $args );

            // Setup Posts Array
            $posts = array();

            // The Loop
            if ( $query->have_posts() ) {
            	while ( $query->have_posts() ) {
            		$query->the_post();

                // better wordpress endpoint post object
                $bwe_post = new stdClass();

                // get post data
                $bwe_post->id = get_the_ID();
                $bwe_post->title = get_the_title();
                $bwe_post->slug = basename(get_permalink());
                $bwe_post->date = get_the_date('c');
                $bwe_post->excerpt = get_the_excerpt();

                // show post content unless parameter is false
                if( $show_content === 'true' ) {
                  $bwe_post->content = apply_filters('the_content', get_the_content());
                }

                $bwe_post->author = esc_html__(get_the_author(), 'text_domain');
                $bwe_post->author_id = get_the_author_meta('ID');

                /*
                 *
                 * get the terms
                 *
                 */
                if( get_object_taxonomies($cpt) ){
                  $cpt_taxonomies = get_object_taxonomies($cpt, 'names');

                  $bwe_post->terms = get_the_terms(get_the_ID(), $cpt_taxonomies);

                } else {
                  $bwe_post->terms = array();
                }

                /*
                 *
                 * return acf fields if they exist
                 *
                 */
                $bwe_post->acf = bwe_get_acf();

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

                  $bwe_post->media = $bwe_thumbnails;
                } else {
                  $bwe_post->media = false;
                }

                // Push the post to the main $post array
                array_push($posts, $bwe_post);
            	}

              // return the post array
              return $posts;

            } else {
              // return empty posts array if no posts
            	return $posts;
            }

            // Restore original Post Data
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
        ) );


    }

  } else {
    return array();
  }
}



/*
 *
 * Add action for cpt endpoint building
 *
 */
add_action( 'rest_api_init', 'bwe_build_cpt_endpoints' );
