<?php
/**
 * Get a collection of posts.
 *
 * @param array $data Options for the function.
 * @return object|null Post object,  * or null if none.
 * @since 0.0.1
 */

function bwe_get_posts( WP_REST_Request $request ) {

  // check for params
  $posts_per_page = $request['per_page']?: '10';
  $page = $request['page']?: '1';
  $category = $request['category']?: null;
  $tag = $request['tag']?: null;

  // WP_Query arguments
  $args = array(
  	'nopaging'               => false,
  	'posts_per_page'         => $posts_per_page,
    'paged'                  => $page,
    'cat'                    => $category,
    'tag_id'                 => $tag,
  );

  // The Query
  $query = new WP_Query( $args );

  // Setup Posts Array
  $posts = array();

  // The Loop
  if ( $query->have_posts() ) {
  	while ( $query->have_posts() ) {
  		$query->the_post();

      // better wordpress endpoint post
      $bwe_post = new stdClass();

      $bwe_post->id = get_the_ID();
      $bwe_post->title = get_the_title();
      $bwe_post->date = get_the_date('c');
      $bwe_post->excerpt = get_the_excerpt();
      $bwe_post->content = get_the_content();

      // get all possible thumbnail sources
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

      array_push($posts, $bwe_post);
  	}
    return $posts;
  } else {
    // return empty posts array
  	return $posts;
  }

  // Restore original Post Data
  wp_reset_postdata();
}

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
      'category' =>  array(
        'validate_callback' => 'is_numeric'
      ),
      'tag' =>  array(
        'validate_callback' => 'is_numeric'
      ),
    ),
  ) );
} );
