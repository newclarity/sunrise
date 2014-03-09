<?php

/**
 * Class _Sunrise_Fields_Helper
 */
class _Sunrise_Fields_Helper {

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
  private static $_multiform_fields = array( 'index' => array() );

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
   * @param string $field_name
   * @param array $args
   * @return Sunrise_Field_Base
   */
  static function field( $field_name, $args = array() ) {
    global $post;
    $field = null;
    $args = Sunrise::ensure_object_type( $args );
    $key = $field_name . md5( serialize( Sunrise::ensure_form_query_args( $args ) ) );
    if ( ! isset( self::$_fields['keyed'][$key] ) ) {
      $fields = Sunrise::get_form_fields( $args );
      if ( isset( $fields[$field_name] ) ) {
        /**
         * @var Sunrise_Field_Base $field
         */
        $field = $fields[$field_name];
        self::$_fields['keyed'][$key] = $field;
      }
    }
    return $field;
  }

  /**
   * @param string $field_name
   * @param array $args
   * @return mixed
   */
  static function field_html( $field_name, $args = array() ) {
    global $post;
    $value = null;
    $field = self::field( $field_name, Sunrise::ensure_object_id( $args ) );
    if ( $field ) {
      $field->set_object_id( $args['object_id'] );
      $value = $field->value();
    }
    return $value;
  }

  /**
   * Add a field to a form that was previously registered with Sunrise::register_field().
   *
   * @param $field_name
   * @return int
   */
  static function add_form_field( $field_name ) {
    $field_index = self::$_multiform_fields['index'][strtolower( $field_name )];
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
   * @param bool $multiform Will (typically) be true if called directly, false if called from self::register_form_field().
   * @return int
   */
  static function register_field( $field_name, $field_args = array(), $multiform = true ) {
    $args['field_name'] = strtolower( $field_name );
    $args['field_index'] = count( self::$_fields['index'] );
    self::$_fields['index'][$args['field_index']] = $field_args;
    if ( $multiform ) {
      /**
       * Create an index of multiform fields
       */
      self::$_multiform_fields['index'][$name] = $args['field_index'];
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

}
_Sunrise_Fields_Helper::on_load();
