<?php

/**
 * Class _Sunrise_Objects_Helper
 */
class _Sunrise_Objects_Helper extends Sunrise_Base{

  /**
   * @var Sunrise_Object_Classifier
   */
  private static $_temp_object_classifier;

  /**
   *
   */
  static function on_load() {
    Sunrise::register_helper( __CLASS__ );
    self::$_temp_object_classifier = new Sunrise_Object_Classifier();
  }

  /**
   * @param array $args
   * @return array
   */
  static function ensure_object_id( $args ) {
    if ( empty( $args['object_id'] ) ) {
      global $post;
      $args['object_id'] = isset( $post->ID ) ? (int)$post->ID : 0;
    }
    return $args;
  }

  /**
   * @param array $args
   * @return array
   */
  static function ensure_object_type( $args ) {
    $args = wp_parse_args( $args );
    if ( empty( $args['object_type'] ) ) {
      global $post;
      $args['object_type'] = isset( $post->post_type ) ? $post->post_type : false;
    }
    if ( ! $args['object_type'] instanceof Sunrise_Object_Classifier ) {
      self::$_temp_object_classifier->assign( $args['object_type'] );
      $args['object_type'] = self::$_temp_object_classifier;
    }
    return $args;
  }


}
_Sunrise_Objects_Helper::on_load();
