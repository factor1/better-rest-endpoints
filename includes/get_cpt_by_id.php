<?php
/**
 * Get Custom Post Type By ID
 *
 * @param array $data Options for the function.
 * @return object|null Return post object,  * or null if none.
 * @since 0.0.1
 */

function bre_build_single_cpt_endpoints() {
  if( bre_get_cpts() ) {

    // store what custom post types we have
    $cpt_collection = bre_get_cpts();

    foreach ($cpt_collection as $key => $cpt) {

      /*
       *
       * Register Rest API Endpoint
       *
       */
      register_rest_route( 'better-rest-endpoints/v1', '/'.$cpt.'/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => function ( WP_REST_Request $request ) use ($cpt) {

            // setup parameters
            $post_id = $request['id'];

            // WP_Query Arguments
            $args = array(
              'post_type'     => $cpt,
              'p'             => $post_id,
            );

            // The Query
            $query = new WP_Query( $args );

            if( $query->have_posts() ){

              // setup post object
              $bre_cpt_post = new stdClass();

              while( $query->have_posts() ) {
                $query->the_post();

                global $post;

                // get post data
                $permalink = get_permalink();
                $bre_cpt_post->id = get_the_ID();
                $bre_cpt_post->title = get_the_title();
                $bre_cpt_post->slug = $post->post_name;
                $bre_cpt_post->permalink = $permalink;
                $bre_cpt_post->date = get_the_date('c');
                $bre_cpt_post->date_modified = get_the_modified_date('c');
                $bre_cpt_post->excerpt = get_the_excerpt();
                $bre_cpt_post->content = apply_filters('the_content', get_the_content());
                $bre_cpt_post->author = esc_html__(get_the_author(), 'text_domain');
                $bre_cpt_post->author_id = get_the_author_meta('ID');
                $bre_cpt_post->author_nicename = get_the_author_meta('user_nicename');

                /*
                 *
                 * get the terms
                 *
                 */
                if( get_object_taxonomies($cpt) ){
                  $cpt_taxonomies = get_object_taxonomies($cpt, 'names');

                  $bre_cpt_post->terms = get_the_terms(get_the_ID(), $cpt_taxonomies);

                } else {
                  $bre_cpt_post->terms = array();
                }


                /*
                 *
                 * return acf fields if they exist
                 *
                 */
                $bre_cpt_post->acf = bre_get_acf();

                /*
                 *
                 * return Yoast SEO fields if they exist
                 *
                 */
                $bre_cpt_post->yoast = bre_get_yoast( $bre_cpt_post->id );

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

                  $bre_cpt_post->media = $bre_thumbnails;
                } else {
                  $bre_cpt_post->media = false;
                }

              }

              return $bre_cpt_post;
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
add_action( 'rest_api_init', 'bre_build_single_cpt_endpoints' );
