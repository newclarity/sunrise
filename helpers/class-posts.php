<?php

/**
 * Class _Sunrise_Posts
 */
class _Sunrise_Posts extends Sunrise_Base{

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
_Sunrise_Posts::on_load();
