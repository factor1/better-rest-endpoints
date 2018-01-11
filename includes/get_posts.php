<?php
/**
 * Get a collection of posts.
 *
 * @param array $request Options for the function.
 * @return array|null Post array,  * or null if none.
 * @since 0.0.1
 */

function bwe_get_posts( WP_REST_Request $request ) {

  // check for params
  $posts_per_page = $request['per_page']?: '10';
  $page = $request['page']?: '1';
  $category = $request['category']?: null;
  $category_name = $request['category_name']?: '';
  $tag = $request['tag']?: null;
  $content = $request['content'];
  $show_content = filter_var($content, FILTER_VALIDATE_BOOLEAN);
  $orderby = $request['orderby']?: null;
  $order = $request['order']?: null;
  $exclude = $request['exclude']?: null;
  $author = $request['author']?: '';

  // WP_Query arguments
  $args = array(
  	'nopaging'               => false,
  	'posts_per_page'         => $posts_per_page,
    'paged'                  => $page,
    'cat'                    => $category,
    'category_name'          => $category_name,
    'tag_id'                 => $tag,
    'order'                  => $order?:'DESC',
    'orderby'                => $orderby?:'date',
    'post__not_in'           => array($exclude),
    'author_name'            => $author
  );

  // The Query
  $query = new WP_Query( $args );

  // Setup Posts Array
  $posts = array();

  // The Loop
  if ( $query->have_posts() ) {
  	while ( $query->have_posts() ) {
  		$query->the_post();

      // For Headers
      $total = $query->found_posts;
      $pages = $query->max_num_pages;

      // better wordpress endpoint post object
      $bwe_post = new stdClass();

      // get post data
      $bwe_post->id = get_the_ID();
      $bwe_post->title = get_the_title();
      $bwe_post->slug = basename(get_permalink());
      $bwe_post->date = get_the_date('c');
      $bwe_post->excerpt = get_the_excerpt();

      // show post content unless parameter is false
      if( $content === null || $show_content === true ) {
        $bwe_post->content = apply_filters('the_content', get_the_content());
      }

      $bwe_post->author = esc_html__(get_the_author(), 'text_domain');
      $bwe_post->author_id = get_the_author_meta('ID');
      $bwe_post->author_nicename = get_the_author_meta('user_nicename');

      /*
       *
       * get category data using get_the_category()
       *
       */
      $categories = get_the_category();

      $bwe_categories = [];
      $bwe_category_ids = [];

      if( !empty($categories) ){
        foreach ($categories as $key => $category) {
          array_push($bwe_category_ids, $category->term_id);
          array_push($bwe_categories, $category->cat_name);
        }
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

      if( !empty($tags) ){
        foreach ($tags as $key => $tag) {
          array_push($bwe_tag_ids, $tag->term_id);
          array_push($bwe_tags, $tag->name);
        }
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
      array_push($posts, $bwe_post);
  	}

    // return the post array
    $response = rest_ensure_response( $posts );
    $response->header( 'X-WP-Total', (int) $total );
    $response->header( 'X-WP-TotalPages', (int) $pages );
    return $response;

  } else {
    // return empty posts array if no posts
  	return $posts;
  }

  // Restore original Post Data
  wp_reset_postdata();
}

/*
 *
 * Register Rest API Endpoint
 *
 */
add_action( 'rest_api_init', function () {
  register_rest_route( 'better-wp-endpoints/v1', '/posts/', array(
    'methods' => 'GET',
    'callback' => 'bwe_get_posts',
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
      'category' =>  array(
        'description'       => 'Get a category from the collection.',
        'type'              => 'integer',
        'validate_callback' => function( $param, $request, $key ) {
          return is_numeric( $param );
         },
        'sanitize_callback' => 'absint'
      ),
      'tag' =>  array(
        'description'       => 'Get a tag from the collection.',
        'type'              => 'integer',
        'validate_callback' => function( $param, $request, $key ) {
          return is_numeric( $param );
         },
        'sanitize_callback' => 'absint'
      ),
      'exclude' =>  array(
        'description'       => 'Exclude a post by ID.',
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
            // $param = true;
            $status = true;
          } else if( $param == 'false' || $param == 'FALSE') {
            //$param = false;
            $status = false;
          }

          return is_bool( $status );
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
        'description'       => 'Change how the collection is ordered.',
        'type'              => 'string',
        'validate_callback' => function($param, $request, $key) {
            return is_string( $param );
          },
        'sanitize_callback' => 'sanitize_text_field',
      ),
      'author' =>  array(
        'description'       => 'Query the collection by author.',
        'type'              => 'string',
        'validate_callback' => function($param, $request, $key) {
            return is_string( $param );
          },
        'sanitize_callback' => 'sanitize_text_field',
      ),
      'category_name' =>  array(
        'description'       => 'Query the collection by category slug.',
        'type'              => 'string',
        'validate_callback' => function($param, $request, $key) {
            return is_string( $param );
          },
        'sanitize_callback' => 'sanitize_text_field',
      ),
    ),
  ) );
} );
