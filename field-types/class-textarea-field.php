<?php

/**
 * Class Sunrise_Textarea_Field
 *
 * @property string $rows
 * @property string $cols
 * @property string $rows_html
 * @property string $cols_html
 *
 */
class Sunrise_Textarea_Field extends Sunrise_Field_Base {
  const HTML_TYPE = 'textarea';

  /**
   * @return array
   */
  function default_args() {
    return array(
      'html_rows' => 5,
      'html_cols' => 50,
    );
  }

}
