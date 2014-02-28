<?php

/**
 * Class Sunrise_Feature_Base
 *
 * @todo Features are intended to be extensible. Plans are to add ability to register new feature classes and then be
 *       able to specify them when defining fields.
 */
abstract class Sunrise_Feature_Base extends Sunrise_Base {
  /**
   * @var Sunrise_Field_Base
   */
  var $owner;

  /**
   *
   */
  function feature_html() {
    return __( 'feature_html() not defined in child class.', 'sunrise' );
  }

}
