<?php

/**
 * Class _Sunrise_Posts_Helper
 */
class _Sunrise_Posts_Helper extends Sunrise_Base{

  /**
   *
   */
  static function on_load() {
    Sunrise::register_helper( __CLASS__ );
  }

  /**
   * @return bool
   */
  static function is_post_edit_screen() {
    global $pagenow;
    return 'post.php' == $pagenow || 'post-new.php' == $pagenow;
  }

}
_Sunrise_Posts_Helper::on_load();
