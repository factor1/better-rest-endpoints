<?php
/**
 * Return Yoast SEO Fields
 *
 * @param none
 * @return object|null Yoast object,  * or null if none.
 * @since 1.3.0
 */

function bre_get_yoast( $post_ID = false ) {

    include_once ( ABSPATH . 'wp-admin/includes/plugin.php' );

    // check if yoast is active before doing anything
    if ( $post_ID && is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {

        // get fields
        $wpseo_frontend = WPSEO_Frontend::get_instance();

        $yoast_fields = array(
            'yoast_wpseo_focuskw'               => get_post_meta( $post_ID, '_yoast_wpseo_focuskw', true ),
            'yoast_wpseo_title'                 => $wpseo_frontend->get_content_title( get_post( $post_ID ) ),
            'yoast_wpseo_metadesc'              => get_post_meta( $post_ID, '_yoast_wpseo_metadesc', true ),
            'yoast_wpseo_linkdex'               => get_post_meta( $post_ID, '_yoast_wpseo_linkdex', true ),
            'yoast_wpseo_metakeywords'          => get_post_meta( $post_ID, '_yoast_wpseo_metakeywords', true ),
            'yoast_wpseo_meta-robots-noindex'   => get_post_meta( $post_ID, '_yoast_wpseo_meta-robots-noindex', true ),
            'yoast_wpseo_meta-robots-nofollow'  => get_post_meta( $post_ID, '_yoast_wpseo_meta-robots-nofollow', true ),
            'yoast_wpseo_meta-robots-adv'       => get_post_meta( $post_ID, '_yoast_wpseo_meta-robots-adv', true ),
            'yoast_wpseo_canonical'             => get_post_meta( $post_ID, '_yoast_wpseo_canonical', true ),
            'yoast_wpseo_redirect'              => get_post_meta( $post_ID, '_yoast_wpseo_redirect', true ),
            'yoast_wpseo_opengraph-title'       => get_post_meta( $post_ID, '_yoast_wpseo_opengraph-title', true ),
            'yoast_wpseo_opengraph-description' => get_post_meta( $post_ID, '_yoast_wpseo_opengraph-description', true ),
            'yoast_wpseo_opengraph-image'       => get_post_meta( $post_ID, '_yoast_wpseo_opengraph-image', true ),
            'yoast_wpseo_twitter-title'         => get_post_meta( $post_ID, '_yoast_wpseo_twitter-title', true ),
            'yoast_wpseo_twitter-description'   => get_post_meta( $post_ID, '_yoast_wpseo_twitter-description', true ),
            'yoast_wpseo_twitter-image'         => get_post_meta( $post_ID, '_yoast_wpseo_twitter-image', true )
        );

        // if we have fields
        if ( $yoast_fields ) {
          return $yoast_fields;
        }

    } else {
        // no yoast, return false
        return false;
    }
}
