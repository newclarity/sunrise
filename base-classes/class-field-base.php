<?php

/**
 * Class Sunrise_Field_Base
 *
 * @property string $layout_class
 * @property string $container_html
 * @property string $html_id
 * @property string $html_type
 * @property string $entry_name
 * @property string $class_html
 * @property string $attributes_html
 */

abstract class Sunrise_Field_Base extends Sunrise_Base {
  const VAR_PREFIX = 'field_';
  const ENTRY_ELEMENT = 'input';
  const HTML_TYPE = 'unspecified';

  /**
   * @var bool|string
   */
  var $field_name = false;

  /**
   * @var bool|string
   */
  var $field_label = false;

  /**
   * @var bool|string
   */
  var $field_prefix = false;

  /**
   * @var bool|string
   */
  var $field_suffix = false;

  /**
   * @var bool
   */
  var $field_required = false;

  /**
   * @var mixed
   */
  var $field_default = null;

  /**
   * @var bool
   */
  var $no_label = false;

  /**
   * @var Sunrise_Form_Base
   */
  var $form;

  /**
   * @var bool|Sunrise_Html_Element
   */
  private $_html_element = false;

  /**
   * @var bool|int
   */
  protected $_field_index = false;

  /**
   * @var null|mixed
   */
  protected $_field_value = null;

  /**
   * @param array $field_args
   */
  function __construct( $field_args = array() ) {
    parent::__construct( $field_args );
    self::initialize( $field_args );
  }

  /**
   * @param array $field_args
   */
  function initialize( $field_args ) {
    // @todo Add error messages here because child class should declare.
  }

  /**
   * @return bool|Sunrise_Html_Element
   */
  function html_element() {
    if ( ! $this->_html_element ) {
      $attributes = $this->extra;
      $attributes['html_id']   = $this->html_id();
      $attributes['html_type'] = $this->html_type();
      $attributes['html_name'] = $this->html_name();
      $attributes = $this->filter_html_attributes( $attributes );
      $this->_html_element = new Sunrise_Html_Element( $this->entry_element(), $attributes, $this->html_value() );
    }
    return $this->_html_element;
  }

  /**
   *
   */
  function html_value() {
    return $this->field_value();
  }

  /**
   *
   */
  function field_value() {
    return $this->_field_value;
  }

  /**
   * @todo This is a temporary solution that needs to get replaced with instance filters or some other structure.
   *
   * @param array $attributes
   * @return array
   */
  function filter_html_attributes( $attributes ) {
    return $attributes;
  }

  /**
   * @return bool|string
   */
  function html_id() {
    return str_replace( '_', '-', $this->field_name );
  }

  /**
   * @return bool|string
   */
  function html_type() {
    return constant( get_class( $this ) . '::HTML_TYPE' );
  }

  /**
   * @return bool|mixed|string
   */
  function entry_element() {
    $entry_element = $this->html_type();
    if ( ! preg_match( '#^(input|select|textarea)$#', $entry_element ) ) {
      $entry_element = constant( get_class( $this ) . '::ENTRY_ELEMENT' );
      if ( empty( $entry_element ) ) {
        $entry_element = 'input';
      }
    }
    return $entry_element;
  }

  /**
   * @return bool|string
   */
  function html_name() {
    return Sunrise::underscorize( $this->field_name );
  }

  /**
   * @return bool|string
   */
  function attributes_html() {
    return $this->html_element()->attributes_html();
  }

  /**
   * @return bool|string
   */
  function element_html() {
    return $this->html_element()->element_html();
  }

  /**
   * @return bool|string
   */
  function label_container_html() {
    return "<span>{$this->field_label}</span>";
  }

  /**
   * @return bool|string
   */
  function container_html() {
    $element = new Sunrise_Html_Element( 'div', array(
      'id' => "{$this->html_id}-field-layput-container",
      'class' => 'field-layput-container',
      ),
      $this->field_entry_html()
    );
    return $element->element_html();
  }

  /**
   * @return string
   */
  function field_layout_html() {
    $label = ! $this->no_label ? $this->label_container_html() : false;
    $element = new Sunrise_Html_Element( 'section', array(
      'id'     => $this->html_id,
      'class' => 'field-layout'
      ),
      "{$label}{$this->container_html}<div class=\"clear\"></div>"
    );
    return $element->element_html();
  }

  /**
   * @return string
   */
  function field_entry_html() {
    return $this->element_html();
  }

}



