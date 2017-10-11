<?php
/**
 * Get Custom Post Type By Slug
 *
 * @param array $data Options for the function.
 * @return object|null Return post object,  * or null if none.
 * @since 0.0.1
 */

function bwe_build_single_cpt_endpoints_slug() {
  if( bwe_get_cpts() ) {

    // store what custom post types we have
    $cpt_collection = bwe_get_cpts();

    foreach ($cpt_collection as $key => $cpt) {

      /*
       *
       * Register Rest API Endpoint
       *
       */
      register_rest_route( 'better-wp-endpoints/v1', '/'.$cpt.'/(?P<slug>\S+)', array(
        'methods' => 'GET',
        'callback' => function ( WP_REST_Request $request ) use ($cpt) {

            // setup parameters
            $post_slug = $request['slug'];

            // WP_Query Arguments
            $args = array(
              'post_type'     => $cpt,
              'p'             => $post_slug,
            );

            // The Query
            $query = new WP_Query( $args );

            if( $query->have_posts() ){

              // setup post object
              $bwe_cpt_post = new stdClass();

              while( $query->have_posts() ) {
                $query->the_post();


                // get post data
                $bwe_cpt_post->id = get_the_ID();
                $bwe_cpt_post->title = get_the_title();
                $bwe_cpt_post->slug = basename(get_permalink());
                $bwe_cpt_post->date = get_the_date('c');
                $bwe_cpt_post->excerpt = get_the_excerpt();
                $bwe_cpt_post->content = apply_filters('the_content', get_the_content());
                $bwe_cpt_post->author = esc_html__(get_the_author(), 'text_domain');
                $bwe_cpt_post->author_id = get_the_author_meta('ID');

                /*
                 *
                 * get the terms
                 *
                 */
                if( get_object_taxonomies($cpt) ){
                  $cpt_taxonomies = get_object_taxonomies($cpt, 'names');

                  $bwe_cpt_post->terms = get_the_terms(get_the_ID(), $cpt_taxonomies);

                } else {
                  $bwe_cpt_post->terms = array();
                }


                /*
                 *
                 * return acf fields if they exist
                 *
                 */
                $bwe_cpt_post->acf = bwe_get_acf();

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

                  $bwe_cpt_post->media = $bwe_thumbnails;
                } else {
                  $bwe_cpt_post->media = false;
                }

              }

              return $bwe_cpt_post;
            } else {
              // if no post is found
              return array();
            }

            // reset post data
            wp_reset_postdata();

          }
      ));

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
add_action( 'rest_api_init', 'bwe_build_single_cpt_endpoints_slug' );
