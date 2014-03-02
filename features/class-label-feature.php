<?php

/**
 * Class Sunrise_Label_Feature
 */
class Sunrise_Label_Feature extends Sunrise_Feature_Base
{

  /**
   *
   */
  function feature_html() {
    $field = $this->owner;
    if ( $field->no_label ) {
      $html = false;
    } else {
      $html = Sunrise::get_element_html( 'div', "html_for={$field->html_name}", "{$field->field_label}:" );
    }
    return $html;
  }
}


