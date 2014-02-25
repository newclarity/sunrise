<?php

/**
 * Class Sunrise_Form_Base
 */
class Sunrise_Form_Base extends Sunrise_Base {
	CONST VAR_PREFIX = 'form_';

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
	 * @var string 'admin' or 'theme'
	 */
	var $form_context;

	/**
	 * @var bool
	 */
	var $form_visible = true;

	var $metabox;

	var $metabox_title;
	var $metabox_callback;
	var $metabox_context;
	var $metabox_priorty;
	var $metabox_callback_args;

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
	 * @param array $form_args
	 * @return array
	 */
	function pre_initialize( $form_args = array() ) {
		$form_args = wp_parse_args( $form_args, array(
			'metabox_title' => __( 'No Title Specified', 'sunrise' ),
			'metabox_callback' => function( $post_type, $post ) {
				 echo __( 'No Metabox Callback Specified', 'sunrise' );
			 },
			'metabox_context' => 'advanced',
			'metabox_priorty' => 'default',
			'callback_args' => null,
		));
		return $form_args;
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
					'field_index' => $field_name,	// yes, this is correct
					'field_name' => $field,
				));
				$this->_fields[$field]->form = $this;
			}
		}
		return $this->_fields;
	}

}
