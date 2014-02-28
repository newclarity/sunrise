<?php

/**
 * Class Sunrise_Label_Feature
 */
class Sunrise_Label_Feature extends Sunrise_Feature_Base {

  /**
   * @return bool|string
   */
  function feature_html() {
    $field = $this->owner;
    return ! $field->no_label ? "<div>{$field->field_label}:</div>" : false;
  }

}
