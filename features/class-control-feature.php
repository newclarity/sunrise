<?php

/**
 * Class Sunrise_Control_Feature
 */
class Sunrise_Control_Feature extends Sunrise_Feature_Base {

  /**
   *
   */
  function feature_html() {
    $field = $this->owner;
    $attributes = array(
      'html_id' => "{$field->html_id}-field-control",
      'html_class' => "field-control",
    );
    return Sunrise::get_element_html( 'div', $attributes, $field->element_html() );
  }

}
