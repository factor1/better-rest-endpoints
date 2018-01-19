<?php
/**
 * Get All Custom Taxonomies
 *
 * @param none
 * @return array returns an array of custom taxonomies
 * @since 0.1.8
 */

function bre_get_custom_tax() {
  $bre_custom_tax = array();

  foreach ( get_taxonomies() as $custom_tax ){
    array_push($bre_custom_tax, $custom_tax);
  }

  if( !empty($bre_custom_tax) ){
    return $bre_custom_tax;
  } else {
    return false;
  }
}
