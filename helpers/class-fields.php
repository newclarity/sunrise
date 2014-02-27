<?php

/**
 * Class _Sunrise_Fields
 */
class _Sunrise_Fields {

  /**
   * @var array
   */
  private static $_fields = array(
    'index' => array(),
    'keyed' => array(),
  );

  /**
   * @var array
   */
  private static $_multiuse_fields = array( 'index' => array() );

  /**
   * @var array
   */
  private static $_field_types = array();

  /**
   *
   */
  static function on_load() {
    Sunrise::register_helper( __CLASS__ );
  }

  /**
   *
   */
  static function _fixup_fields() {
    $fields = self::$_fields['index'];
    foreach( $fields as $field_index => $field_args ) {
      if ( isset( $field_args['type'] ) && ! isset( $field_args['field_type'] ) ) {
        $field_args['field_type'] = $field_args['type'];
        unset( $field_args['type'] );
        $fields[$field_index] = $field_args;
      }
    }
    self::$_fields['index'] = $fields;
  }

  /**
   * Add a field to a form that was previously registered with Sunrise::register_field().
   *
   * @param $field_name
   * @return int
   */
  static function add_form_field( $field_name ) {
    $field_index = self::$_multiuse_fields['index'][strtolower( $field_name )];
    Sunrise::_index_field( $field_name, $field_index );
    return $field_index;
  }


  /**
   * @param $field_name
   * @param bool|array $field_args
   * @return int
   */
  static function register_form_field( $field_name, $field_args = array() ) {
    Sunrise::_index_field( $field_name, count( self::$_fields['index'] ) );
    $field_name = strtolower( $field_name );
    return self::register_field( $field_name, $field_args, false );
  }

  /**
   * @param $field_name
   * @param array $field_args
   * @param bool $multiuse Will (typically) be true if called directly, false if called from self::register_form_field().
   * @return int
   */
  static function register_field( $field_name, $field_args = array(), $multiuse = true ) {
    $args['field_name'] = strtolower( $field_name );
    $args['field_index'] = count( self::$_fields['index'] );
    self::$_fields['index'][$args['field_index']] = $field_args;
    if ( $multiuse ) {
      /**
       * Create an index of multiuse fields
       */
      self::$_multiuse_fields['index'][$name] = $args['field_index'];
    }
    return $args['field_index'];
  }

  /**
   * @param string $field_type
   * @param string|array $field_args Class name or array of args
   * @param bool|string $filepath
   */
  static function register_field_type( $field_type, $field_args, $filepath = false ) {
    $field_type = strtolower( $field_type );
    if ( isset( $filepath ) ) {
      $field_args = (object)array(
        'field_args'   => $field_args,
        'filepath'     => $filepath,
      );
    }
    self::$_field_types[$field_type] = $field_args;
  }

  /**
   * @param $field
   * @return bool
   */
  static function is_field( $field ) {
    return is_subclass_of( $field, 'Sunrise_Field_Base' );
  }

  /**
   * Create a New Field object
   *
   * @param $field_args
   * @return Sunrise_Field_Base
   */
  static function create_field( $field_args ) {
    /**
     *
     */
    $field = false;
    if ( isset( $field_args['field_index'] ) ) {
      $field_args = array_merge( self::$_fields['index'][$field_args['field_index']], $field_args );
    }
    if ( ! isset( $field_args['field_type'] ) ) {
      $field_args['field_type'] = 'text';
    }
    $field_type = self::_get_field_type( $field_args['field_type'] );
    if ( is_object( $field_type ) ) {
      /**
       * Field type is Class name with external filepath
       */
      if ( $field_type->filepath ) {
        require_once( $field_type->filepath );
      }
      $field_type = $field_type->field_args;
    }
    if ( is_string( $field_type ) && class_exists( $field_type ) ) {
      /**
       * Field type is a Class name
       */
      $field = new $field_type( $field_args );
    } else if ( is_array( $field_type ) ) {
      /**
       * Field type is a 'Prototype'
       */
      $field_args = wp_parse_args( $field_args, $field_type );
      $field = self::create_field( $field_args );
    }
    return $field;
  }

  /**
   * @param string $field_type
   * @return string|array|object
   */
  private static function _get_field_type( $field_type ) {
    return self::$_field_types[$field_type];
  }

}
_Sunrise_Fields::on_load();
