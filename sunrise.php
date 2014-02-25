<?php
/**
 * Plugin Name: Sunrise
 */

require( __DIR__ . '/base-classes/class-base.php' );

/**
 * Class Sunrise
 *
 * @mixin _Sunrise_Context
 * @mixin _Sunrise_Fields
 * @mixin _Sunrise_Forms
 * @mixin _Sunrise_Post_Admin_Forms
 */
class Sunrise extends Sunrise_Base {
	/**
	 * @var array
	 */
	private static $_helpers = array();

	/**
	 *
	 */
	static function on_load() {

		require( __DIR__ . '/core-classes/class-object-classifier.php' );
		require( __DIR__ . '/core-classes/class-metabox.php' );

		require( __DIR__ . '/base-classes/class-form-base.php' );
		require( __DIR__ . '/base-classes/class-post-form-base.php' );
		require( __DIR__ . '/base-classes/class-field-base.php' );

		require( __DIR__ . '/helpers/class-context.php' );
		require( __DIR__ . '/helpers/class-forms.php' );
		require( __DIR__ . '/helpers/class-fields.php' );
		require( __DIR__ . '/helpers/class-post-admin-forms.php' );

		require( __DIR__ . '/field-types/class-text-field.php' );
		require( __DIR__ . '/field-types/class-textarea-field.php' );
		require( __DIR__ . '/field-types/class-url-field.php' );

		require( __DIR__ . '/form-types/class-post-admin-form.php' );

		self::register_form_type( 'post_admin', 'Sunrise_Post_Admin_Form' );

		self::register_field_type( 'text', 			'Sunrise_Text_Field' );
		self::register_field_type( 'textarea', 	'Sunrise_Textarea_Field' );
		self::register_field_type( 'url', 			'Sunrise_Url_Field' );

		self::add_static_action( 'wp_loaded' );

	}

	/**
	 *
	 */
	static function _wp_loaded() {
		self::_fixup_forms();
		self::_fixup_fields();
	}

	/**
	 * @param string $string
	 * @return string
	 */
	static function dashize( $string ) {
		return str_replace( array( '_', ' ' ), '-', $string );
	}

	/**
	 * @param string $string
	 * @return string
	 */
	static function underscorize( $string ) {
		return str_replace( array( '-', ' ' ), '_', $string );
	}

	/**
  * Register a Helper Class for the Main class.
  *
  * @param string $class_name
  */
 static function register_helper( $class_name ) {
   self::$_helpers[] = $class_name;
 }

	/**
  * Register a Helper Class for the Main class.
  *
	 * @param string $method_name
	 * @param bool|string $class_name
  */
 	static function register_helper_method( $method_name, $class_name = false ) {
 		if ( ! $class_name ) {
 			$class_name = get_called_class();
		}
   	self::$_helpers[$method_name] = $class_name;
 	}

	/**
	 * Add a prefix to a class $arg if needed based on the property names of the class.
	 *
	 * @param string $object_name
	 * @param string $prefix
	 * @param string $base_class
	 * @return int
	 */
	static function maybe_prefix_class_arg( $object_name, $prefix, $base_class ) {
		static $regex = array();
		$prefix = preg_quote( $prefix );
		if ( ! isset( $regex[$base_class] ) ) {
			$regex = array();
			$reflector = new ReflectionClass( $base_class );
			foreach( $reflector->getProperties() as $property ) {
				if ( preg_match( "#^_?{$prefix}(.*?)$#", $property->name, $match ) ) {
					$regex[] = $match[1];
				}
			}
			$regex[$base_class] = '#^(' . implode( '|', $regex ) . ')$#i';
		}
		return strtolower( preg_replace( $regex[$base_class], "{$prefix}$1", $object_name ) );
	}


	/**
  * Delegate calls to other "helper" classes.
  *
  * @param string $method_name
  * @param array $args
  *
  * @return mixed
  *
  * @throws Exception
  */
	static function __callStatic( $method_name, $args ) {
		static $found;
		if ( ! isset( $found ) ) {
			$found = array();
			foreach( self::$_helpers as $this_method_name => $this_class_name ) {
				if ( ! is_numeric( $this_method_name ) ) {
					$found[$this_method_name] = $this_class_name;
					unset( self::$_helpers[$this_method_name] );
				}
			}
		}
		if ( isset( $found[$method_name] ) ) {
			$value = call_user_func_array( array( $found[$method_name], $method_name ), $args );
		} else {
			foreach( self::$_helpers as $index => $class_name ) {
				if ( method_exists( $class_name, $method_name ) ) {
					$value = call_user_func_array( array( $class_name, $method_name ), $args );
					$found[$method_name] = $class_name;
					break;
				}
			}
			if ( ! isset( $found[$method_name] ) ) {
				$message = __( 'ERROR: Neither %s nor any of it\'s registered helper classes have the method %s().', 'exo' );
				trigger_error( sprintf( $message, get_called_class(), $method_name ), E_USER_WARNING );
				$value = null;
			}
		}
		return $value;
	}

}
Sunrise::on_load();
