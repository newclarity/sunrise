<?php

/**
 * Class _Sunrise_Post_Admin_Forms
 */
class _Sunrise_Post_Admin_Forms extends Sunrise_Form_Base {

  /**
   *
   */
  static function on_load() {
//    Sunrise::add_static_filter( __CLASS__, 'default_title', 2 );
//    Sunrise::add_static_action( __CLASS__, 'wp_insert_post_data', 2 );
//    Sunrise::add_static_action( __CLASS__, 'save_post', 2 );

    self::add_static_action( 'add_meta_boxes' );
    self::add_static_action( 'edit_form_after_title' );
    Sunrise::register_helper( __CLASS__ );
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
    $form->the_form_layout();
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
        if ( $form->form_visible ) {
          $form->add_meta_box();
          //self::set_meta_box_view_state($form->view_state, $metabox_name, $post_type );
        }
      }
    }
  }


}
_Sunrise_Post_Admin_Forms::on_load();


