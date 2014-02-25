<?php

/**
 * Class Sunrise_Post_Form_Base
 *
 * @property string $post_type
 */
class Sunrise_Post_Form_Base extends Sunrise_Form_Base {

	/**
	 * @var Sunrise_Metabox
	 */
	private $_metabox;

	/**
	 * @var string
	 */
	var $metabox_title;

	/**
	 * @var string
	 */
	var $metabox_context;

	/**
	 * @var string
	 */
	var $metabox_priority;

	/**
	 * @param array $form_args
	 */
	function initialize( $form_args = array() ) {
		if ( ! isset( $form_args['object_type'] ) ) {
			$form_args['object_type'] = 'post/post';
		}
		$this->set_object_type( $form_args['object_type'] );
	}

	/**
	 * @return string
	 */
	function post_type() {
		return $this->object_type()->subtype;
	}

	/**
	 * @return Sunrise_Metabox
	 */
	function metabox() {
		if ( ! isset( $this->_metabox ) ) {
			$this->_metabox = new Sunrise_Metabox(
				Sunrise::dashize( "{$this->post_type}-{$this->form_name}-metabox" ),
				array(
					'title' => $this->metabox_title,
					'callback' => array( $this, 'the_metabox' ),
					'screen' => $this->post_type(),
					'context' => $this->metabox_context,
					'priority' => $this->metabox_priority,
					'callback_args' => array( 'form' => $this ),
			));
		}
		return $this->_metabox;
	}

	/**
	 * \
	 */
	function add_meta_box() {
		$this->metabox()->add_meta_box();
	}

	/**
	 * \
	 */
	function the_meta_box() {
	}

	/**
	 * @return string
	 */
	function the_form_layout() {
		echo $this->get_form_layout_html();
	}

	/**
	 * @return string
	 */
	function get_form_layout_html() {
		$html = array( '<div id="sunrise-post-admin-form-' . $this->form_name . '">' );
		/**
		 * @var Sunrise_Field_Base $field
		 */
		foreach( $this->get_fields() as $field_name => $field ) {
			$html[] = $field->get_field_layout_html();
		}
		$html[] = '</div>';
		return implode( $html );
	}

}
