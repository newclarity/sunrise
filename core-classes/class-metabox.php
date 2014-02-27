<?php

/**
 * Class Sunrise_Metabox
 */
class Sunrise_Metabox extends Sunrise_Base {
  CONST VAR_PREFX = 'metabox_';

  /**
   * @var string
   */
  private $_metabox_id;

  /**
   * @var string
   */
  var $metabox_title;

  /**
   * @var callable
   */
  var $metabox_callback;

  /**
   * @var string
   */
  var $metabox_screen;

  /**
   * @var string
   */
  var $metabox_context;

  /**
   * @var string
   */
  var $metabox_priorty;

  /**
   * @var array
   */
  var $callback_args;

  /**
   * @param array $metabox_id
   * @param array $args
   */
  function __construct( $metabox_id, $args = array() ) {
    $this->_metabox_id = $metabox_id;
    $args = wp_parse_args( $args, array(
      'metabox_title' => __( 'No Title Specified', 'sunrise' ),
      'metabox_callback' => function( $post_type, $post ) {
         echo __( 'No Metabox Callback Specified', 'sunrise' );
       },
      'metabox_context' => 'advanced',
      'metabox_priorty' => 'default',
      'callback_args' => null,
    ));
    parent::__construct( $args );
  }

  /**
   *
   */
  function add_meta_box() {
    add_meta_box(
      $this->metabox_id(),
      $this->metabox_title,
      $this->metabox_callback,
      $this->metabox_screen,
      $this->metabox_context,
      $this->metabox_priorty,
      $this->callback_args
    );
  }

  function metabox_id() {
    return "{$this->_metabox_id}-metabox";
  }

}
