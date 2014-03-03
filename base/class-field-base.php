<?php

/**
 * Class Sunrise_Field_Base
 *
 * @property string $html_id
 * @property string $html_name
 * @property string $html_type
 * @property string $attributes_html
 */

abstract class Sunrise_Field_Base extends Sunrise_Base {

  /**
   *
   */
  const VAR_PREFIX = 'field_';

  /**
   *
   */
  const NO_PREFIX = 'value|features';

  /**
   *
   */
  const CONTROL_TAG = 'input';

  /**
   *
   */
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
  protected $_value = null;

  /**
   * @var array
   */
  protected $_features = false;

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
      $attributes['html_value'] = $this->html_value();
      $attributes = $this->filter_html_attributes( $attributes );
      $this->_html_element = new Sunrise_Html_Element( $this->control_tag(), $attributes, $this->value() );
    }
    return $this->_html_element;
  }

  /**
   *
   */
  function html_value() {
    return $this->value();
  }

  /**
   *
   */
  function value() {
    if ( is_null( $this->_value ) ) {
      $this->_value = get_post_meta( $this->object_id(), $this->meta_key(), true );
    }
    return $this->_value;
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
   * @return int
   */
  function object_id() {
    return $this->form->object_id;
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
   * The tag for the 'Control" that this field uses, i.e. input, textarea, select, etc.
   *
   * @return bool|mixed|string
   */
  function control_tag() {
    $control_tag = $this->html_type();
    if ( ! preg_match( '#^(input|select|textarea)$#', $control_tag ) ) {
      $control_tag = constant( get_class( $this ) . '::CONTROL_TAG' );
      if ( empty( $control_tag ) ) {
        $control_tag = 'input';
      }
    }
    return $control_tag;
  }

  /**
   * @return bool|string
   */
  function html_name() {
    return 'sunrise_fields[' . Sunrise::underscorize( $this->field_name ) . ']';
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
   * @return stdClass
   *
   * @todo Features are meant to be extensible. Plans are to add ability to register new feature classes and be able to
   *       specify them when defining fields.  Plans are also to allow only the features that are needed to be loaded.
   */
  function get_features() {
    if ( ! $this->_features ) {
      $this->_features = array(
        'label'   => new Sunrise_Label_Feature(),
        'control' => new Sunrise_Control_Feature(),
        'help'    => new Sunrise_Help_Feature(),
        'message' => new Sunrise_Message_Feature(),
        'infobox' => new Sunrise_Infobox_Feature(),
      );
      /**
       * @var Sunrise_Feature_Base $feature
       */
      foreach( $this->_features as $feature ) {
        $feature->owner = $this;
      }
      $this->_features = (object)$this->_features;
    }
    return $this->_features;
  }

  /**
   * @param null|mixed $value
   */
  function update_value( $value = null ) {
    if ( is_null( $value ) ) {
      $value = $this->_value;
    } else {
      $this->set_value( $value );
    }
    update_post_meta( $this->object_id(), $this->meta_key(), esc_sql( $value ) );
  }

  /**
   * @param mixed $value
   */
  function set_value( $value ) {
    $this->_value = $value;
  }

  /**
   * Name used for meta_key
   *
   * @return string
   */
  function meta_key() {
    return "_sf[{$this->field_name}]";
  }

  /**
   * @return string
   */
  function field_html() {
    $features = $this->get_features();
    $element = new Sunrise_Html_Element( 'section', array(
      'id'    => $this->html_id,
      'class' => 'field-layout'
      ),
      "{$features->label->feature_html}{$features->control->feature_html}<div class=\"clear\"></div>"
    );
    return $element->element_html();
  }

  /**
   * @param string $field_name
   * @param array $args
   * @return Sunrise_Field_Base
   */
  function field( $field_name, $args ) {
    if ( isset( $this->form->object_id ) ) {
      $args['object_id'] = $this->form->object_id;
    }
    return Sunrise::field( $field_name, $args );
  }

  /**
   * @param $object_id
   */
  function set_object_id( $object_id ) {
    if ( property_exists( $this->form, 'object_id' ) ) {
      $this->form->object_id = $object_id;
    }
  }

}




