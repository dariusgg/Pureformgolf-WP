<?php

/**
 * Handle admin panel
 */
class SocialFans_Counter_Panel {

  public function __construct () {

  }

  /**
   * show plugin form
   * 
   * @param array $socials
   * @param array $cache_periods
   * @param array $numbers_format
   */
  public function view ( $socials , $cache_periods , $numbers_format ) {

    $is_update = false;

    if ( isset( $_GET['update'] ) && $_GET['update'] == '1' ) {
      $is_update = true;
    }

    $envato_sites = array ();
    $envato_sites['themeforest'] = 'Themeforest';
    $envato_sites['codecanyon'] = 'Codecanyon';
    $envato_sites['3docean'] = '3docean';
    $envato_sites['activeden'] = 'Activeden';
    $envato_sites['audiojungle'] = 'Audiojungle';
    $envato_sites['graphicriver'] = 'Graphicriver';
    $envato_sites['photodune'] = 'Photodune';
    $envato_sites['videohive'] = 'Videohive';

    include_once SOCIALFANS_COUNTER_PATH . 'includes/templates/socialfans-counter-panel-html.php';
  }

  public function viewSticky( $socials ) {

      $is_update = false;

      if ( isset( $_GET['update'] ) && $_GET['update'] == '1' ) {
          $is_update = true;
      }

      include_once SOCIALFANS_COUNTER_PATH . 'includes/templates/socialfans-counter-panel-sticky-html.php';
  }

  public function viewDebug() {

      $list = sfc_debug_list();
      include_once SOCIALFANS_COUNTER_PATH . 'includes/templates/socialfans-counter-panel-debug-html.php';
  }

  /**
   * get an hidden input
   * 
   * @param string $input_name
   * @param string $input_value
   * @param string $input_id
   * @return string
   */
  public function getHiddenElement ( $input_name , $input_value , $input_id = '' ) {

    if ( empty( $input_id ) ) {
      $input_id = $input_name;
    }

    $element = '<input name="' . $input_name . '" id="' . $this->generateId( $input_id ) . '" type="hidden" value="' . $input_value . '" />';

    return $element;
  }

  /**
   * get an text input
   *
   * @param string $label_text
   * @param string $input_name
   * @param string $input_value
   * @param string $input_id
   * @return string
   */
  public function getTextElement ( $label_text , $input_name , $input_value , $input_id = '' , $type = 'text' , $width = '300px' , $class = '' ) {

    if ( empty( $input_id ) ) {
      $input_id = $input_name;
    }

    $element = '<label for="' . $this->generateId( $input_id ) . '">' . $label_text . '</label> ';
    $element .= '<input name="' . $input_name . '" id="' . $this->generateId( $input_id ) . '" type="' . $type . '" value="' . $input_value . '" class="' . $class . '" style="width: ' . $width . ' ;" />';

    return $element;
  }

  /**
   * get an checkbox input
   *
   * @param string $input_name
   * @param string $input_value
   * @param string $input_id
   * @return string
   */
  public function getCheckboxElement ( $input_name , $input_value , $input_id = '' ) {

    if ( empty( $input_id ) ) {
      $input_id = $input_name;
    }

    $checked = '';

    if ( $input_value == 1 ) {
      $checked = 'checked="checked"';
    }

    $element = '<input name="' . $input_name . '" id="' . $this->generateId( $input_id ) . '" type="checkbox" value="1" ' . $checked . ' />';

    return $element;
  }

  /**
   *  get an select list
   * 
   * @param string $label_text
   * @param string $input_name
   * @param array $options
   * @param string $input_value
   * @param string $input_id
   * @return string
   */
  public function getSelectElement ( $label_text , $input_name , $options , $input_value , $input_id = '' ) {

    if ( empty( $input_id ) ) {
      $input_id = $input_name;
    }


    $element = '<label for="' . $this->generateId( $input_id ) . '">' . $label_text . '</label> ';
    $element .= '<select name="' . $input_name . '" id="' . $this->generateId( $input_id ) . '">';

    if ( is_array( $options ) ) {

      foreach ( $options as $opt_key => $opt_value ) {

        $selected = '';

        if ( $input_value == $opt_key ) {
          $selected = 'selected="selected"';
        }

        $element .= '<option value="' . $opt_key . '" ' . $selected . ' >' . $opt_value . '</option>';
      }
    }

    $element .= '</select>';


    return $element;
  }

  /**
   * get url for social docs
   * 
   * @param string $social
   * @return string
   */
  public function getSocialDocs ( $social ) {
    return sprintf( __( 'Need help for %s? click %s' , 'sfcounter' ) , '<strong>' . ucfirst( $social ) . '</strong>' , '<strong><a href="' . SOCIALFANS_COUNTER_DOCS_URL . '#' . $social . '" target="_blank">' . __( 'here' , 'sfcounter' ) . '</a></strong>' );
  }

  private function generateId ( $id ) {
    return str_replace( array ( '[' , '][' , ']' ) , array ( '_' , '_' , '' ) , $id );
  }

}
