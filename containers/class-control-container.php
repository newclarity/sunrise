<?php

/**
 * Class Sunrise_Control_Container
 */
class Sunrise_Control_Container extends Sunrise_Container_Base {

  /**
   *
   */
  function container_html() {
    $field = $this->owner;
    $element = new Sunrise_Html_Element( 'div', array(
      'id' => "{$field->html_id}-field-layput-container",
      'class' => 'field-layput-container',
      ),
      $field->element_html()
    );
    return $element->element_html();
  }

}
