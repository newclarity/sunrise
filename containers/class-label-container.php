<?php

/**
 * Class Sunrise_Label_Container
 */
class Sunrise_Label_Container extends Sunrise_Container_Base {

  /**
   * @return bool|string
   */
  function container_html() {
    $field = $this->owner;
    return ! $field->no_label ? "<div>{$field->field_label}:</div>" : false;
  }

}
