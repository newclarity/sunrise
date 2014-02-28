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
    $element = new Sunrise_Html_Element( 'div', array(
      'id' => "{$field->html_id}-field-control",
      'class' => 'field-control',
      ),
      $field->element_html()
    );
    return $element->element_html();
  }

}
