<?php

/**
 * Class _Sunrise_Html_Elements_Helper
 */
class _Sunrise_Html_Elements_Helper {

  /**
   * @var array
   */
  private static $_element_attributes = array();

  /**
   *
   */
  static function on_load() {
    Sunrise::register_helper( __CLASS__ );
  }

  /**
   * @param string $tag_name
   * @param array $attributes
   * @param mixed $value
   * @return Sunrise_Html_Element
   */
  static function get_element_html( $tag_name, $attributes, $value ) {
    $html_element = self::get_html_element( $tag_name, $attributes, $value, true );
    return $html_element->element_html();
  }

  /**
   * @param string $tag_name
   * @param array $attributes
   * @param mixed $value
   * @param bool $reuse
   * @return Sunrise_Html_Element
   */
  static function get_html_element( $tag_name, $attributes, $value, $reuse = false ) {
    if ( ! $reuse ) {
      $element = new Sunrise_Html_Element( $tag_name, $attributes, $value );
    } else {
      /**
       * @var Sunrise_Html_Element $reusable_element
       */
      static $reusable_element = false;
      if ( ! $reusable_element ) {
        $reusable_element = new Sunrise_Html_Element( $tag_name, $attributes, $value );
      } else {
        $reusable_element->reset_element( $tag_name, $attributes, $value );
      }
      $element = $reusable_element;
    }
    return $element;
  }

  /**
   * @param $html_element
   * @return array
   */
  static function get_html_attributes( $html_element ) {
    if ( ! isset( self::$_element_attributes[$html_element] ) ) {

      /**
       * @see http://www.w3.org/TR/html5/dom.html#global-attributes
       */
      $attributes = array(
        'accesskey', 'class', 'contenteditable', 'dir', 'draggable', 'dropzone',
        'hidden', 'id', 'lang', 'spellcheck', 'style', 'tabindex', 'title', 'translate'
      );

      switch ( $html_element ) {

        case 'input':
          $more_attributes = array(
            'accept', 'alt', 'autocomplete', 'autofocus', 'autosave', 'checked', 'dirname', 'disabled',
            'form', 'formaction', 'formenctype', 'formmethod', 'formnovalidate', 'formtarget',
            'height', 'inputmode', 'list', 'max', 'maxlength', 'min', 'minlength', 'multiple',
            'name', 'pattern', 'placeholder', 'readonly', 'required', 'selectionDirection',
            'size', 'src', 'step', 'type', 'value', 'width'
          );
          break;

        case 'textarea':
          $more_attributes = array( 'cols', 'name', 'rows', 'tabindex', 'wrap' );
          break;

        case 'label':
          $more_attributes = array( 'for', 'form' );
          break;

        case 'ul':
          $more_attributes = array( 'compact', 'type' );
          break;

        case 'ol':
          $more_attributes = array( 'compact', 'reversed', 'start', 'type' );
          break;

        case 'li':
          $more_attributes = array( 'type', 'value' );
          break;

        case 'section':
        case 'div':
        case 'span':
        default:
          $more_attributes = false;
          break;
      }

      if ( $more_attributes ) {
        $attributes = array_merge( $attributes, $more_attributes );
      }

      self::$_element_attributes[$html_element] = array_fill_keys( $attributes, false );

    }
    return self::$_element_attributes[$html_element];
  }

}
_Sunrise_Html_Elements_Helper::on_load();








