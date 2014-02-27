<?php

/**
 * Class Sunrise_Form_Base
 */
class Sunrise_Form_Base extends Sunrise_Base {

  /**
   *
   */
  const VAR_PREFIX = 'form_';

  /**
   *
   */
  const NO_PREFIX = 'fields';

  /**
   *
   */
  const FORM_CONTEXT = 'unspecified';

  /**
   * @var int
   */
  var $object_id;

  /**
   * @var string
   */
  var $form_name = 'main';

  /**
   * @var string
   */
  var $form_label;

  /**
   * @var bool
   */
  var $form_visible = true;

  /**
   * @var int
   */
  protected $_form_index = false;

  /**
   * @var string|Sunrise_Object_Classifier
   */
  protected $_object_type = false;

  /**
   * @var array
   */
  protected $_fields;

  /**
   * @return array
   */
  function default_args() {
    return array(
          'metabox_title' => __( 'No Title Specified', 'sunrise' ),
          'metabox_callback' => function( $post_type, $post ) {
               echo __( 'No Metabox Callback Specified', 'sunrise' );
           },
          'metabox_context' => 'advanced',
          'metabox_priorty' => 'default',
    );
  }

  /**
   * @return string
   */
  function form_context() {
    return constant( get_class( $this ) . '::FORM_CONTEXT' );
  }

  /**
   * @return Sunrise_Object_Classifier
   */
  function object_type() {
      if ( $this->_object_type && ! is_a( $this->_object_type, 'Sunrise_Object_Classifier' ) ) {
        $this->_object_type = new Sunrise_Object_Classifier( $this->_object_type );
      }
      return $this->_object_type;
  }

  /**
   * @param string $object_type
   */
  function set_object_type( $object_type ) {
    $this->_object_type = $object_type;
  }

  function get_fields() {
    foreach( $this->_fields as $field_name => $field ) {
      if ( is_numeric( $field_name ) ) {
        unset( $this->_fields[$field_name] );
        $this->_fields[$field] = Sunrise::create_field( array(
          'field_index' => $field_name,  // yes, this is correct
          'field_name' => $field,
        ));
        $this->_fields[$field]->form = $this;
      }
    }
    return $this->_fields;
  }

  /**
   * @return string
   */
  function the_form_layout() {
    echo $this->form_layout_html();
  }

  /**
   * @return string
   */
  function form_layout_html() {
    return __( 'Form Layout not defined in child class.', 'sunrise' );
  }
}
