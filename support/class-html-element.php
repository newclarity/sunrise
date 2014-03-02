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
  var $tag_name;

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
   * @param string $tag_name
   * @param array $attributes
   * @param null|callable|string $value
   */
  function __construct( $tag_name, $attributes = array(), $value = null ) {
    $this->reset_element( $tag_name, $attributes, $value );
  }

  /**
   * @param string $tag_name
   * @param array $attributes
   * @param null|callable|string $value
   */
  function reset_element( $tag_name, $attributes = array(), $value = null ) {
    $this->tag_name = $tag_name;
    $this->_attributes = wp_parse_args( $attributes );
    $this->element_value = $value;
    $this->_attributes_parsed = false;
  }

  /**
   * @return bool
   */
  function is_void_element() {
    return preg_match( '#^(' . self::VOID_ELEMENTS . ')$#i', $this->tag_name ) ? true : false;
  }

  /**
   * @return array
   */
  function element_html() {
    $html = "<{$this->tag_name} " . $this->attributes_html() . '>';
    if ( ! $this->is_void_element() ) {
      $value = is_callable( $this->element_value ) ? call_user_func( $this->element_value, $this ) : $this->element_value;
      $html .= "{$value}</{$this->tag_name}>";
    }
    return $html;
  }

  /**
   * @return array
   */
  function attributes_html() {
    $valid_attributes = Sunrise::get_html_attributes( $this->tag_name );
    $html = array();
    $attributes = array_filter( $this->attributes() );
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
      $attributes = Sunrise::get_html_attributes( $this->tag_name );
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
   * @param $attribute_name
   * @return mixed
   */
  function get_attribute( $attribute_name ) {
    $attributes = $this->attributes();
    return ! empty( $attributes[$attribute_name] ) ? $attributes[$attribute_name] : false;
  }

  /**
   * @param $attribute_name
   * @return mixed
   */
  function get_attribute_html( $attribute_name ) {
    $value = $this->get_attribute( $attribute_name );
    return $value ? " {$attribute_name}=\"{$value}\"" : false;
  }

}
