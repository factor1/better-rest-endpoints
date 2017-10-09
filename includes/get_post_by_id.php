<?php
/**
 * Grab latest post by ID
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

      // better wordpress endpoint post object
      $bwe_post = new stdClass();

      $bwe_post->id = get_the_ID();
      $bwe_post->title = get_the_title();
      $bwe_post->slug = basename(get_permalink());
      $bwe_post->date = get_the_date('c');
      $bwe_post->excerpt = get_the_excerpt();
      $bwe_post->content = apply_filters('the_content', get_the_content());
      $bwe_post->author = esc_html__(get_the_author(), 'text_domain');
      $bwe_post->author_id = get_the_author_meta('ID');

      /*
       *
       * get category data using get_the_category()
       *
       */
      $categories = get_the_category();

      $bwe_categories = [];
      $bwe_category_ids = [];

      foreach ($categories as $key => $category) {
        array_push($bwe_category_ids, $category->term_id);
        array_push($bwe_categories, $category->cat_name);
      }

      $bwe_post->category_ids = $bwe_category_ids;
      $bwe_post->category_names = $bwe_categories;

      /*
       *
       * get tag data using get_the_tags()
       *
       */
      $tags = get_the_tags();

      $bwe_tags = [];
      $bwe_tag_ids = [];

      foreach ($tags as $key => $tag) {
        array_push($bwe_tag_ids, $tag->term_id);
        array_push($bwe_tags, $tag->name);
      }

      $bwe_post->tag_ids = $bwe_tag_ids;
      $bwe_post->tag_names = $bwe_tags;

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
      return $bwe_post;

  	}
  } else {
  	// no posts found
    $bwepost = [];

    return $bwe_post;
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
