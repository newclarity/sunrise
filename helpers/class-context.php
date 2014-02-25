<?php

/**
 * Class _Sunrise_Context
 */
class _Sunrise_Context {

	/**
	 * @var bool 'add', 'edit' or 'ajax'
	 */
	private static $_form_mode = false;

	/**
	 *
	 */
	static function on_load() {
		Sunrise::register_helper( __CLASS__ );
	}

	/*
	 * Returns a mode of 'add', 'edit' or 'ajax' depending on the current mode for this form.
	 *
	 * @return bool|string Mode or false if not on a data entry page.
	 */
	static function get_form_mode() {
		if ( ! self::$_form_mode ) {
			global $pagenow;
			if ( 'post-new.php' == $pagenow )
				self::$_form_mode = 'add';
			else if ( 'admin-ajax.php' == $pagenow )
				self::$_form_mode = 'ajax';  // TODO: Do we need more than this?
			else if ( 'post.php' == $pagenow )
				if ( ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) ||
						 ( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] ) )
				self::$_form_mode = 'edit';
		}
		return self::$_form_mode;
	}

}
_Sunrise_Context::on_load();
