<?php

/**
 * Class Sunrise_Html_Element
 */
class Sunrise_Html_Element extends Sunrise_Base {
  const VOID_ELEMENTS = 'area|base|br|col|command|embed|hr|img|input|keygen|link|meta|param|source|track|wbr';
  const VAR_PREFIX = 'html_';

  /**
   * @var string
   */
  var $element_name;

  /**
   * @var string
   */
  var $element_value;

  /**
   * @var array
   */
  private $_attributes;

  /**
   * @var bool
   */
  private $_attributes_parsed;

  /**
   * @param string $element_name
   * @param array $attribute_args
   * @param null|callable|string $element_value
   */
  function __construct( $element_name, $attribute_args = array(), $element_value = null ) {
    $this->element_name = $element_name;
    $this->_attributes = $attribute_args;
    $this->element_value = $element_value;
  }

  /**
   * @return bool
   */
  function is_void_element() {
    return preg_match( '#^(' . self::VOID_ELEMENTS . ')$#i', $this->element_name ) ? true : false;
  }

  /**
   * @return array
   */
  function element_html() {
    $html = "<{$this->element_name} " . $this->attributes_html() . '>';
    if ( ! $this->is_void_element() ) {
      $value = is_callable( $this->element_value ) ? call_user_func( $this->element_value, $this ) : $this->element_value;
      $html .= "{$value}</{$this->element_name}>";
    }
    return $html;
  }

  /**
   * @return array
   */
  function attributes_html() {
    $valid_attributes = Sunrise::get_html_attributes( $this->element_name );
    $html = array();
    $attributes = array_filter( $this->attributes() );
    $attributes['value'] = $this->element_value;
    foreach( $attributes as $name => $value ) {
      if ( $value && isset( $valid_attributes[$name] ) ) {
        $html[] = "{$name}=\"{$value}\"";
      }
    }
    return implode( ' ', $html );
  }

  /**
   * @return array
   */
  function attributes() {
    if ( ! $this->_attributes_parsed ) {
      $attributes = Sunrise::get_html_attributes( $this->element_name );
      foreach( $this->_attributes as $name => $value ) {
        if ( preg_match( '#^html_(.*?)$#', $name, $match ) ) {
          $attributes[sanitize_key( $match[1] )] = esc_attr( $value );
        }
      }
      $this->_attributes = $attributes;
      $this->_attributes_parsed = true;
    }
    return $this->_attributes;
  }

  /**
   * @param $element_name
   * @return mixed
   */
  function get_attribute( $element_name ) {
    $attributes = $this->attributes();
    return ! empty( $attributes[$element_name] ) ? $attributes[$element_name] : false;
  }

  /**
   * @param $element_name
   * @return mixed
   */
  function get_attribute_html( $element_name ) {
    $value = $this->get_attribute( $element_name );
    return $value ? " {$element_name}=\"{$value}\"" : false;
  }

}
