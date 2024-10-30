<?php
//add custom css in the in the editor
  function cdashmm_pricing_table_block_css(){
    cdash_enqueue_styles();
    wp_enqueue_style(
        'cdashmm_pricing_table_styles',
        plugins_url( './css/pricing_table_block.css', dirname(__FILE__) ),
        array(),
        null
     );
  }
  add_action( 'enqueue_block_assets', 'cdashmm_pricing_table_block_css' );

  function cdashmm_get_custom_fields(){
    $cdash_options = get_option('cdash_directory_options');
    if(isset($cdash_options['bus_custom']) && is_array($cdash_options['bus_custom']) && array_filter($cdash_options['bus_custom']) != [] ) {
      $customfields = $cdash_options['bus_custom'];
    }else{
      $customfields = [];
    }
    $response = array();
    $response = $customfields;
    echo json_encode($response);
    wp_die();
  }
  add_action('wp_ajax_cdash_custom_fields', 'cdashmm_get_custom_fields');
add_action('wp_ajax_nopriv_cdash_custom_fields', 'cdashmm_get_custom_fields');

?>