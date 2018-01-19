<?php
/**
 * Return ACF Fields
 *
 * @param none
 * @return object|null ACF object,  * or null if none.
 * @since 0.0.1
 */

function bre_get_acf() {
  
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

  // check if acf is active before doing anything
   if( is_plugin_active('advanced-custom-fields-pro/acf.php') || is_plugin_active('advanced-custom-fields/acf.php') ) {

     // get fields
     $acf_fields = get_fields();

     // if we have fields
     if( $acf_fields ) {
       return $acf_fields;
     }

   } else {
     // no acf, return false
     return false;
   }
}
