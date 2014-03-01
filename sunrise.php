<?php
/**
 * Plugin Name: Sunrise
 * Plugin URI: http://github.com/newclarity/sunrise
 * Description: Forms and Fields for WordPress
 * Version: 2.0-alpha
 * Author: MikeSchinkel
 * Author URI: http://about.me/mikeschinkel
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright 2010-2014 NewClarity LLC.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 */

require( __DIR__ . '/base/class-base.php' );

/**
 * Class Sunrise
 *
 * @mixin _Sunrise_Fields_Helper
 * @mixin _Sunrise_Forms_Helper
 * @mixin _Sunrise_Posts_Helper
 * @mixin _Sunrise_Post_Admin_Forms_Helper
 * @mixin _Sunrise_Html_Elements_Helper
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
    /**
     * @todo Plan to implement an autoloader for some of these.
     */
    require( __DIR__ . '/support/class-object-classifier.php' );
    require( __DIR__ . '/support/class-metabox.php' );
    require( __DIR__ . '/support/class-html-element.php' );

    require( __DIR__ . '/base/class-form-base.php' );
    require( __DIR__ . '/base/class-post-form-base.php' );
    require( __DIR__ . '/base/class-field-base.php' );
    require( __DIR__ . '/base/class-feature-base.php');

    require( __DIR__ . '/features/class-control-feature.php' );
    require( __DIR__ . '/features/class-help-feature.php' );
    require( __DIR__ . '/features/class-label-feature.php' );
    require( __DIR__ . '/features/class-message-feature.php' );
    require( __DIR__ . '/features/class-infobox-feature.php' );

    require( __DIR__ . '/helpers/class-posts-helper.php' );
    require( __DIR__ . '/helpers/class-forms-helper.php' );
    require( __DIR__ . '/helpers/class-fields-helper.php' );
    require( __DIR__ . '/helpers/class-html-elements-helper.php' );
    require( __DIR__ . '/helpers/class-post-admin-forms-helper.php' );

    require( __DIR__ . '/fields/class-text-field.php' );
    require( __DIR__ . '/fields/class-textarea-field.php' );
    require( __DIR__ . '/fields/class-url-field.php' );

    require( __DIR__ . '/forms/class-post-admin-form.php' );

    self::register_form_type( 'post_admin', 'Sunrise_Post_Admin_Form' );

    self::register_field_type( 'text',     'Sunrise_Text_Field' );
    self::register_field_type( 'textarea', 'Sunrise_Textarea_Field' );
    self::register_field_type( 'url',      'Sunrise_Url_Field' );

    /**
     * @todo Evaluate if priority 10 is okay or priority 0 is needed for these 'admin_*' hooks.
     */
    if ( defined( 'DOING_AJAX' ) ) {
      self::add_static_action( 'admin_init', 'wp_loaded' );
    } else if ( is_admin() ) {
      self::add_static_action( 'admin_menu', 'wp_loaded' );
    } else {
      self::add_static_action( 'wp_loaded' );
    }

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
    static $found = false;
    if ( ! $found ) {
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

  /**
   * Grabs the current or a new WP_screen object.
   *
   * Tries to get the current one but if it's not available then it hacks it's way to recreate one
   * because WordPress does not consistently set it, and it's not our place to change it's state.
   * We just want what we want.
   *
   * @return WP_Screen
   */
  static function get_screen() {
    $screen = get_current_screen();
    if ( empty( $screen ) ) {
      global $hook_suffix, $page_hook, $plugin_page, $pagenow, $current_screen;
      if ( empty( $hook_suffix ) ) {
        $save_hook_suffix = $hook_suffix;
        $save_current_screen = $current_screen;
        if ( isset($page_hook) )
          $hook_suffix = $page_hook;
        else if ( isset($plugin_page) )
          $hook_suffix = $plugin_page;
        else if ( isset($pagenow) )
          $hook_suffix = $pagenow;
        set_current_screen();
        $screen = get_current_screen();
        $hook_suffix = $save_hook_suffix;
        $current_screen = $save_current_screen;
      }
    }
    return $screen;
  }
}
Sunrise::on_load();
