<?php
/**
 * Get All Custom Taxonomies
 *
 * @param none
 * @return array returns an array of custom taxonomies
 * @since 0.1.8
 */

function bwe_get_custom_tax() {
  $bwe_custom_tax = array();

  foreach ( get_taxonomies() as $custom_tax ){
    array_push($bwe_custom_tax, $custom_tax);
  }

  if( !empty($bwe_custom_tax) ){
    return $bwe_custom_tax;
  } else {
    return false;
  }
}
