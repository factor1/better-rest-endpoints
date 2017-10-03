<?php
/**
 * Grab latest post title by an author!
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 * @since 0.0.1
 */

function get_post_by_id( $data ) {
  // WP_Query arguments
  $args = array(
    'p' => $data['id']
  );

  // The Query
  $query = new WP_Query( $args );

  // The Loop
  if ( $query->have_posts() ) {
  	while ( $query->have_posts() ) {
  		$query->the_post();

      $title = get_the_title();

      return $title;
  	}
  } else {
  	// no posts found
  }

  // Restore original Post Data
  wp_reset_postdata();
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'better-wp-endpoints/v1', '/post/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_post_by_id',
  ) );
} );
