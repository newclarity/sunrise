<?php

/**
 * Class _Sunrise_Forms_Helper
 */
class _Sunrise_Forms_Helper extends Sunrise_Base {

  /**
   * @var array
   */
  private static $_forms = array(
    'index' => array(),
    'keyed' => array(),
  );

  private static $_allowed_form_args = array(
    'form_name',
    'form_context',
    'object_type',
  );

  /**
   * @var array
   */
  private static $_form_types = array();

  /**
   * @var int
   */
  private static $_form_index = false;

  /**
   * @var string
   */
  private static $_form_mode;

  /**
   *
   */
  static function on_load() {
    self::$_allowed_form_args = array_flip( self::$_allowed_form_args );
    Sunrise::register_helper( __CLASS__ );
  }

  /**
   * @param array $form_args
   * @return array
   */
  static function get_forms( $form_args = array() ) {
    $form_args = Sunrise::ensure_object_type( $form_args );
    if ( 0 == count( $form_args ) ) {
      $forms = self::$_forms['index'];
    } else {
      $key = array();
      ksort( $form_args );
      foreach( $form_args as $name => $value ) {
        $value = self::_normalize_form_args( $name, $value );
        if ( $value ) {
          $key[] = "{$name}={$value}";
        }
      }
      $keyed = self::$_forms['keyed'];
      if ( 0 == count( $key ) ) {
        $forms = array();
      } else {
        $key = implode( '&', $key );
        $forms = isset( $keyed[$key] ) ? $keyed[$key] : array();
      }
    }
    return $forms;
  }

  /**
   * Ensure that the $args passed in contain all form query $args and only form query $args.
   *
   * @param array $query_args
   * @return array
   */
  static function ensure_form_query_args( $query_args = array() ) {
    $query_args = wp_parse_args( $query_args, $default_args = array(
      'object_type'  => false,
      'form_context' => false,
      'form_name'    => false,
    ));
    return array_intersect_key( $query_args, $default_args );
  }

  /**
   *
   */
  static function get_form_fields( $query_args = array() ) {
    $value = false;
    $object_id = isset( $query_args['object_id'] ) ? $query_args['object_id'] : false;
    $query_args = self::ensure_form_query_args( $query_args );
    $forms = Sunrise::get_forms( $query_args );
    $fields = array();
    /**
     * @var Sunrise_Form_Base $form
     */
    foreach( $forms as $index => $form ) {
      $form->object_id = $object_id;
      $fields = array_merge( $form->get_fields(), $fields );
    }
    return $fields;
  }

  /**
   * @param string $context
   * @return bool|array
   */
  static function get_context_forms( $context ) {
    $by_context = self::$_forms['by_context'];
    return isset( $by_context[$context] ) ? $by_context[$context] : false;
  }

  /**
   * @param string $object_type The target object type; typically a post type with implied 'post'
   * @param array $args
   * @return int
   */
  static function register_form( $object_type, $args = array() ) {
  	$defaults = array(
  		'classes' => array( 'post-admin-form' )
  	);
  	$args = array_merge( $defaults, $args );
    $args['object_type'] = $object_type;
    $args['form_index'] = count( self::$_forms['index'] );
    $args['fields'] = array();
    self::$_forms['index'][] = $args;
    self::$_form_index = $args['form_index'];
    return self::$_form_index;
  }

  /**
   * @return int
   */
  static function form_index() {
    return self::$_form_index;
  }

  /**
   * @param int $form_index
   */
  static function set_form_index( $form_index ) {
    self::$_form_index = $form_index;
  }

  /**
   * @param string $form_type
   * @param string|array $form_args Class name or array of args
   * @param bool|string $filepath
   */
  static function register_form_type( $form_type, $form_args, $filepath = false ) {
    $form_type = strtolower( $form_type );
    if ( $filepath ) {
      $form_args = (object)array(
        'form_args'   => $form_args,
        'filepath'     => $filepath,
      );
    }
    self::$_form_types[$form_type] = $form_args;
  }

  /**
   * Create a New Form object
   *
   * @param $form_args
   * @return Sunrise_Form_Base
   */
  static function create_form( $form_args ) {
    $form_args = wp_parse_args( $form_args, array(
      'form_type' => 'post_admin',
    ));
    $form_type = self::_get_form_type( $form_args['form_type'] );
    if ( is_object( $form_type ) ) {
      /**
       * Form type is Class name with external filepath
       */
      require_once( $form_type->filepath );
      $form_args = $form_args->form_args;
    }
    if ( is_string( $form_type ) && class_exists( $form_type ) ) {
      /**
       * Form type is a Class name
       */
      $form = new $form_type( $form_args );
    } else if ( is_array( $form_type ) ) {
      /**
       * Form type is a 'Prototype'
       */
      $form_args = wp_parse_args( $form_args, $form_type );
      $form = self::create_form( $form_args );
    }
    return $form;
  }

  /*
   * Returns a mode of 'add', 'edit' or 'ajax' depending on the current mode for this form.
   *
   * @TODO Integrate/modify/trash this. It is from old Sunrise.
   *
   * @return bool|string Mode or false if not on a data entry page.
   */
  static function form_mode() {
    if ( ! self::$_form_mode ) {
      global $pagenow;
      if ( 'post-new.php' == $pagenow )
        self::$_form_mode = 'add';
      else if ( 'admin-ajax.php' == $pagenow )
        self::$_form_mode = 'ajax';  // TODO: Do we need more than this?
      else if ( 'post.php' == $pagenow )
        if ( ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) ||
             ( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] ) )
        self::$_form_mode = 'edit';
    }
    return self::$_form_mode;
  }

  /*****************************************************************/
  /* Private and "Internal" methods below
  /*****************************************************************/

  /**
   * @param string $form_type
   * @return string|array|object
   */
  private static function _get_form_type( $form_type ) {
    if ( ! isset( self::$_form_types[$form_type] ) ) {
      $form_type = 'post_admin';
    }
    return self::$_form_types[$form_type];
  }

  /**
   * Fixup forms
   */
  static function _fixup_forms() {
    foreach( self::$_forms['index'] as $form_index => $form_args ) {
      $form = Sunrise::create_form( $form_args );
      self::$_forms['keyed'] += self::_get_keyed_index( $form, array(
        'form_name' => $form->form_name,
        'form_context' => $form->form_context(),
        'object_type' => $form->object_type(),
      ));
    }
  }

  /**
   * @param string $field_name
   * @param int $field_index
   */
  static function _index_field( $field_name, $field_index ) {
    self::$_forms['index'][self::$_form_index]['fields'][$field_index] = $field_name;
  }


  /**
   * Ensure the object_types are Sunrise_Object_Classifier and not a string.
   *
   * @param string $name
   * @param string|Sunrise_Object_Classifier $value
   * @return mixed
   */
  private static function _normalize_form_args( $name, $value ) {
    return $value;
  }


  /**
   * Add a form to a form that was previously registered with Sunrise::register_form().
   *
   * @param array $form_args
   * @return array
   */
  static function normalize_form_args( $form_args ) {
    $form_args = wp_parse_args( $form_args );
    foreach( $form_args as $name => $value ) {
      if ( 'object_type' == $name && ! $value instanceof Sunrise_Object_Classifier ) {
        $value = new Sunrise_Object_Classifier( $value );
      }
    }
    return $form_args;
  }

  /**
   * Returns a keyed index of forms based on a form and it's form args.
   *
   * @param Sunrise_Form_Base $form
   * @param array $form_args
   * @return array
   */
  private static function _get_keyed_index( $form, $form_args ) {
    $index = array();
    $form_args = array_intersect_key( $form_args, self::$_allowed_form_args );
    foreach( $form_args as $name => $value ) {
      $value = self::_normalize_form_args( $name, $value );
      $index["{$name}={$value}"][] = $form;
    }
    $keys = array_keys( $index );
    sort( $keys );
    $index["{$keys[0]}&{$keys[1]}"][] = $form;
    $index["{$keys[0]}&{$keys[2]}"][] = $form;
    $index["{$keys[1]}&{$keys[2]}"][] = $form;
    $index["{$keys[0]}&{$keys[1]}&{$keys[2]}"][] = $form;
    return $index;
  }

}
_Sunrise_Forms_Helper::on_load();
