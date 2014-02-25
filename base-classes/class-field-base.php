<?php

/**
 * Class Sunrise_Field_Base
 */
abstract class Sunrise_Field_Base extends Sunrise_Base {

	var $field_name = false;
	var $field_label = false;
	var $field_type = false;
	var $field_size = false;
	var $field_prefix = false;
	var $field_suffix = false;
	var $field_required = false;
	var $field_default = null;

	var $form;

	protected $_field_index = false;
	protected $_field_value = null;

	/**
	 * @param array $field_args
	 */
	function __construct( $field_args = array() ) {
		self::initialize( $field_args );
		parent::__construct( $field_args );
	}

	function initialize( $field_args ) {
		// @todo Add error messages here because child class should declare.
	}

	function field_id() {
		return "{$this->form->form_name}_{$this->field_name}";
	}

	function html_id() {
		return str_replace( '_', '-', $this->field_id() );
	}

	function get_field_layout_html() {
		return "<div id=\"{$this->html_id}\">{$this->field_label}: {$this->field_name}</div>";
	}

}
