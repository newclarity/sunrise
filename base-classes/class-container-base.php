<?php

/**
 * Class Sunrise_Container_Base
 */
abstract class Sunrise_Container_Base extends Sunrise_Base {
  /**
   * @var Sunrise_Field_Base
   */
  var $owner;

  /**
   *
   */
  function container_html() {
    return __( 'container_html() not defined in child class.', 'sunrise' );
  }

}
