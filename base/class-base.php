<?php

/**
 * Class Sunrise_Base
 */
abstract class Sunrise_Base {
  /**
   * The property (var) prefix from a constant to be used for this current class.
   *
   * @example: const VAR_PREFIX = 'form_';
   *
   * Intended to be used by subclasses.
   */
  const VAR_PREFIX = false;

  /**
   * Pipe-separated field names that should not get a prefix.
   *
   * @example: const NO_PREFIX = 'name|age|rank';
   *
   * Intended to be used by subclasses.
   */
  const NO_PREFIX = false;

  /**
   * @var array
   */
  var $extra = array();

  /**
   * @param array $args
   */
  function __construct( $args = array() ) {
    /**
     * @todo Handle calling default_args() up the inheritance tree.
     */
    $args = wp_parse_args( $args, $this->default_args() );
    if ( $this->do_assign( $args ) ) {
      $args = $this->pre_assign( $args );
      $this->assign( $args );
    }
    $this->initialize( $args );
  }

  /**
   * Returns the default arguments for this class.
   *
   * Intended to be used by subclasses.
   *
   * @return array
   */
  function default_args( $args = array() ) {
    return array();
  }

  /**
   * Called before $this->assign( $args ) to allow bypassing $this->assign() call by returning false.
   *
   * Intended to be used by subclasses.
   *
   * @param array $args
   * @return bool
   */
  function do_assign( $args = array() ) {
    return true;
  }

  /**
   * Called before $this->assign( $args ) to allow filtering of incoming $args before the default $args.
   *
   * Should return an optionally modified array of $args.
   *
   * Intended to be used by subclasses.
   *
   * @param array $args
   * @return array|bool
   */
  function pre_assign( $args = array() ) {
  return $args;
  }

  /**
   * Called to handle additional initializations or corrections after $this->assign( $args ).
   *
   * Intended to be used by subclasses.
   *
   * @param array $args
   */
  function initialize( $args = array() ) {
  }

  /**
   * Gets the property (var) prefix from a constant to be used for this current class.
   *
   * @example: const VAR_PREFIX = 'form_';
   *
   * Intended to be used by subclasses.
   *
   * @return array
   */
  function get_var_prefix() {
    return constant( get_class( $this ) . '::VAR_PREFIX' );
  }

  /**
   * Gets the pipe-separated field names that should not get a prefix.
   *
   * @example: const NO_PREFIX = 'name|age|rank';
   *
   * Intended to be used by subclasses.
   *
   * @return array
   */
  function get_no_prefix() {
    return constant( get_class( $this ) . '::NO_PREFIX' );
  }

  /*
   * Assign the element values in the $args array to the properties of this object.
   *
   * @param array $args An array of name/value pairs that can be used to initialize an object's properties.
   */
  function assign( $args ) {
    if ( false !== ( $var_prefix = $this->get_var_prefix() ) ) {
      $no_prefix = $this->get_no_prefix();
      foreach( $args as $name => $value ) {
        if ( false === strpos( $name, '_' ) && ! preg_match( "#^({$no_prefix})$#", $name ) ) {
          $args["{$var_prefix}{$name}"] = $value;
          unset( $args[$name] );
        }
      }
    }
    foreach( $args as $name => $value ) {
      if ( method_exists( $this, $method_name = "set_{$name}" ) ) {
        call_user_func( array( $this, $method_name ), $value );
      } else if ( property_exists( $this, $name ) ) {
        $this->{$name} = $value;
      } else if ( self::non_public_property_exists( $property_name = "_{$name}" ) ) {
        $this->{$property_name} = $value;
      } else {
        $this->extra[$name] = $value;
      }
    }
  }

  /**
   *
   */
  static function non_public_property_exists( $property ) {
    $reflection = new ReflectionClass( get_called_class() );
    if ( ! $reflection->hasProperty( $property ) ) {
      $exists = false;
    } else {
      $property = $reflection->getProperty( $property );
      $exists = $property->isProtected() || $property->isPrivate();
    }
    return $exists;
  }

  /**
    * @param string $action
    * @param bool|int|callable $callable_or_priority
    * @param int $priority
    *
    * @return bool|void
    */
   function add_action( $action, $callable_or_priority = false, $priority = 10 ) {
     self::add_filter( $action, $callable_or_priority, $priority );
     return $this;
   }

   /**
    * @param string $filter
    * @param bool|int|callable $callable_or_priority
    * @param int $priority
    *
    * @return bool|void
    */
  function add_filter( $filter, $callable_or_priority = false, $priority = 10 ) {
     if ( false === $callable_or_priority ) {
       $callable = array( $this, "_{$filter}" );
     } else if ( is_callable( $callable_or_priority ) ) {
       $callable = $callable_or_priority;
     } else if ( is_numeric( $callable_or_priority ) ) {
       $callable = array( $this, "_{$filter}" );
       $priority = $callable_or_priority;
     }
     if ( 10 <> $priority && isset( $callable[1] ) && ! preg_match( "#_{$priority}$#", $callable[1] ) ) {
       $callable[1] .= "_{$priority}";
     }
     add_filter( $filter, $callable, $priority, 99 );
     return $this;
   }

  /**
    * @param string $action
    * @param bool|int|string|array $method_or_priority
    * @param int $priority
    *
    * @return bool|void
    */
  static function add_static_action( $action, $method_or_priority = false, $priority = 10 ) {
     return self::add_static_filter( $action, $method_or_priority, $priority );
  }

  /**
    * @param string $filter
    * @param bool|int|string|array $method_or_priority
    * @param int $priority
    *
    * @return bool|void
    */
  static function add_static_filter( $filter, $method_or_priority = false, $priority = 10 ) {
     $class = get_called_class();
     if ( is_string( $method_or_priority ) ) {
       $callable = array( $class, "_{$method_or_priority}" );
     } else {
       $callable = array( $class, "_{$filter}" );
       if ( is_numeric( $method_or_priority ) ) {
         $priority = $method_or_priority;
       }
     }
     if ( 10 <> $priority && isset( $callable[1] ) && ! preg_match( "#_{$priority}$#", $callable[1] ) ) {
       $callable[1] .= "_{$priority}";
     }
     return add_filter( $filter, $callable, $priority, 99 );
   }


  /**
   * @param string $property_name
   * @return mixed|null
   */
  function __get( $property_name ) {
    if ( method_exists( $this, $property_name ) ) {
      $value = call_user_func( array( $this, $property_name ) );
    } else {
      $message = __( 'Object of class %s does not contain a property or method named %s().', 'sunrise' );
      trigger_error( sprintf( $message, get_class( $this ), $property_name ), E_USER_WARNING );
      $value = null;
    }
    return $value;
  }

}
