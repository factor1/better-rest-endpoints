<?php
/**
 * Grab latest post by ID
 *
 * @param array $data Options for the function.
 * @return string|null Post data by id,  * or empty object if none.
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

      global $post;

      // better wordpress endpoint post object
      $bre_post = new stdClass();

      $permalink = get_permalink();
      $bre_post->id = get_the_ID();
      $bre_post->title = get_the_title();
      $bre_post->slug = $post->post_name;
      $bre_post->permalink = $permalink;
      $bre_post->date = get_the_date('c');
      $bre_post->date_modified = get_the_modified_date('c');
      $bre_post->excerpt = get_the_excerpt();
      $bre_post->content = apply_filters('the_content', get_the_content());
      $bre_post->author = esc_html__(get_the_author(), 'text_domain');
      $bre_post->author_id = get_the_author_meta('ID');
      $bre_post->author_nicename = get_the_author_meta('user_nicename');

      /*
       *
       * get category data using get_the_category()
       *
       */
      $categories = get_the_category();

      $bre_categories = [];
      $bre_category_ids = [];

      if( !empty($categories) ){
        foreach ($categories as $key => $category) {
          array_push($bre_category_ids, $category->term_id);
          array_push($bre_categories, $category->cat_name);
        }
      }

      $bre_post->category_ids = $bre_category_ids;
      $bre_post->category_names = $bre_categories;

      /*
       *
       * get tag data using get_the_tags()
       *
       */
      $tags = get_the_tags();

      $bre_tags = [];
      $bre_tag_ids = [];

      if( !empty($tags) ){
        foreach ($tags as $key => $tag) {
          array_push($bre_tag_ids, $tag->term_id);
          array_push($bre_tags, $tag->name);
        }
      }

      $bre_post->tag_ids = $bre_tag_ids;
      $bre_post->tag_names = $bre_tags;

      /*
       *
       * return acf fields if they exist
       *
       */
      $bre_post->acf = bre_get_acf();

      /*
       *
       * return Yoast SEO fields if they exist
       *
       */
      $bre_post->yoast = bre_get_yoast( $bre_post->id );

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

        $bre_post->media = $bre_thumbnails;
      } else {
        $bre_post->media = false;
      }

      // Push the post to the main $post array
      return $bre_post;

  	}
  } else {
  	// no posts found
    $bre_post = [];

    return $bre_post;
  }

  // Restore original Post Data
  wp_reset_postdata();
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'better-rest-endpoints/v1', '/post/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_post_by_id',
  ) );
} );
