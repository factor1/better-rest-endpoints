<?php
/**
 * bre_get_cpts()
 * Get Post Types, but filter out core WP and ACF post types
 *
 * TODO: Simplify the allowed post types, example: https://github.com/postpromoterpro/post-promoter-pro/blob/master/includes/general-functions.php#L89
 *
 * @param N/A
 * @return array returns an array of post type names
 * @since 0.0.1
 */

function bre_get_cpts() {

  $bre_cpts = array();

  foreach ( get_post_types( '', 'names', 'and' ) as $post_type ) {

    if( $post_type !== 'post'
    && $post_type !== 'page'
    && $post_type !== 'attachment'
    && $post_type !== 'revision'
    && $post_type !== 'nav_menu_item'
    && $post_type !== 'custom_css'
    && $post_type !== 'customize_changeset'
    && $post_type !== 'acf-field-group'
    && $post_type !== 'acf-field'){
      array_push($bre_cpts, $post_type);
    }
  }

  $bre_cpts = apply_filters( 'better_rest_endpoints_cpt_collection', $bre_cpts );

  // check if array is empty, returns array if data exists, return false if empty
  if( !empty($bre_cpts) ){
    return $bre_cpts;
  } else {
    return false;
  }
}
