<?php

/**
 * Class _Sunrise_Post_Admin_Forms_Helper
 */
class _Sunrise_Post_Admin_Forms_Helper extends Sunrise_Form_Base {

  /**
   *
   */
  static function on_load() {
//    Sunrise::add_static_filter( __CLASS__, 'default_title', 2 );
//    Sunrise::add_static_action( __CLASS__, 'wp_insert_post_data', 2 );
//    Sunrise::add_static_action( __CLASS__, 'save_post', 2 );

    self::add_static_action( 'admin_init' );
    Sunrise::register_helper( __CLASS__ );
  }

  /**
   *
   */
  static function _admin_init() {
    if ( Sunrise::is_post_edit_screen() ) {
      self::add_static_action( 'add_meta_boxes' );
      self::add_static_action( 'edit_form_after_title' );
      self::add_static_action( 'save_post_' . Sunrise::get_screen()->post_type, 'save_post', 3 );
    }
  }

  /**
   * @param int $post_id
   * @param WP_Post $post
   * @param bool $update
   */
  static function _save_post_3( $post_id, $post, $update ) {
    $forms = self:: get_post_admin_forms( $post->post_type );
    if ( count( $forms ) && isset( $_POST['sunrise_fields'] ) && is_array( $POST_fields = $_POST['sunrise_fields'] ) ) {
      /**
       * @var Sunrise_Form_Base $form
       */
      foreach( $forms as $form_name => $form ) {
        $form->object_id = $post_id;
        /**
         * @var Sunrise_Field_Base $field
         */
        foreach( $form->get_fields() as $field_name => $field ) {
          if ( isset( $POST_fields[$field_name] ) ) {
            $field->update_value( $POST_fields[$field_name] );
          }
        }
      }
    }
  }

  /**
   * @param $post_type
   * @return array
   */
  static function get_post_admin_forms( $post_type ) {
    return Sunrise::get_forms( "form_context=admin&object_type=post/{$post_type}" );
  }

  /**
   * @param string $post_type
   * @param string $form_name
   * @return Sunrise_Form_Base
   */
  static function get_post_admin_form( $post_type, $form_name ) {
    $forms = Sunrise::get_forms( "form_context=admin&form_name={$form_name}&object_type=post/{$post_type}" );
    return count( $forms ) ? $forms[0] : false;
  }

  /**
   * Displays the main edit form after the title and above the body editor.
   *
   * @param WP_Post $post
   */
  static function _edit_form_after_title( $post ) {
    /**
     * @var Sunrise_Post_Admin_Form $form
     */
    $form = Sunrise::get_post_admin_form( $post->post_type, 'main' );
    $form->object_id = (int)$post->ID;
    $form->the_form();
  }

  /**
   * @param string $post_type
   * @param WP_Post $post
   */
  static function _add_meta_boxes( $post_type, $post ) {
    $forms = Sunrise::get_post_admin_forms( $post_type );
    if ( count( $forms ) ) {
      /**
       * @var Sunrise_Post_Admin_Form $form
       */
      foreach( $forms as $form_name => $form ) {
        if ( 'main' != $form_name && ! $form->form_hidden ) {
          $form->add_meta_box();
        }
      }
    }
  }


}
_Sunrise_Post_Admin_Forms_Helper::on_load();


