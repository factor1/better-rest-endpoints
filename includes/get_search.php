<?php
/**
 * Get a collection of posts by search term.
 *
 * @param array $request Options for the function.
 * @return array|null Post array,  * or null if none.
 * @since 0.0.1
 */

function bre_get_search( WP_REST_Request $request ) {

  // check for params
  $posts_per_page = $request['per_page']?: '10';
  $page = $request['page']?: '1';
  $category = $request['category']?: null;
  $tag = $request['tag']?: null;
  $content = $request['content'];
  $show_content = filter_var($content, FILTER_VALIDATE_BOOLEAN);
  $acf = $request['acf'];
  $show_acf = filter_var($acf, FILTER_VALIDATE_BOOLEAN);
  $yoast = $request['yoast'];
  $show_yoast = filter_var($yoast, FILTER_VALIDATE_BOOLEAN);
  $media = $request['media'];
  $show_media = filter_var($media, FILTER_VALIDATE_BOOLEAN);
  $search = $request['search']?: null;

  // WP_Query arguments
  $args = array(
  	'nopaging'               => false,
  	'posts_per_page'         => $posts_per_page,
    'paged'                  => $page,
    'cat'                    => $category,
    'tag_id'                 => $tag,
    's'                      => $search
  );

  // The Query
  $query = new WP_Query( $args );

  // Setup Posts Array
  $posts = array();

  // The Loop
  if ( $query->have_posts() ) {
  	while ( $query->have_posts() ) {
  		$query->the_post();

      global $post;

      // For Headers
      $total = $query->found_posts;
      $pages = $query->max_num_pages;

      // better wordpress endpoint post object
      $bre_post = new stdClass();

      // get post data
      $permalink = get_permalink();
      $bre_post->id = get_the_ID();
      $bre_post->title = get_the_title();
      $bre_post->slug = $post->post_name;
      $bre_post->permalink = $permalink;
      $bre_post->date = get_the_date('c');
      $bre_post->date_modified = get_the_modified_date('c');
      $bre_post->excerpt = get_the_excerpt();

      // show post content unless parameter is false
      if( $content === null || $show_content === true ) {
        $bre_post->content = apply_filters('the_content', get_the_content());
      }

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
       * return acf fields if they exist and depending on query string
       *
       */
      if( $acf === null || $show_acf === true ) {
        $bre_post->acf = bre_get_acf();
      }

      /*
       *
       * return Yoast SEO fields if they exist and depending on query string
       *
       */
      if( $yoast === null || $show_yoast === true ) {
        $bre_post->yoast = bre_get_yoast( $bre_post->id );
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

          $bre_post->media = $bre_thumbnails;
        } else {
          $bre_post->media = false;
        }
      }

      // Push the post to the main $post array
      array_push($posts, $bre_post);
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
  register_rest_route( 'better-rest-endpoints/v1', '/search/', array(
    'methods' => 'GET',
    'callback' => 'bre_get_search',
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
      'search' =>  array(
        'description'       => 'The search term used to fetch the collection.',
        'type'              => 'string',
        'required'          => true,
        'validate_callback' => function($param, $request, $key) {
            return is_string( $param );
          },
        'sanitize_callback' => 'sanitize_text_field'
      ),
    ),
  ) );
} );
