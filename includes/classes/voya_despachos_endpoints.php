<?php
if (!class_exists('VoyaDespachosEndpoints')) {
  class VoyaDespachosEndpoints {

    function __construct() {
      add_action( 'rest_api_init', function () {
        register_rest_route( 'voyacl/v1', '/settings/get', array(
          'methods' => 'POST',
          'callback' => [$this,'voyacl_get_settings'],
          'permission_callback' => '__return_true'
        ) );
      });
    }
    
    function voyacl_get_settings( $data ) {
      $response = ["status" => 0];
      if(
        isset($data['apikey']) && !empty($data['apikey'])
        ){
        $options = get_option('woocommerce_voya_despachos_settings',[]);
        if(isset($options['api']) && $options['api'] != '' && $options['api'] == $data['apikey']){
          $response['status'] = 1;
          $response['plugin_version'] = get_plugin_data(VOYA_PLUGIN_PATH.'/voya-despachos.php')['Version'];
          $response['settings'] = $options;
          unset($response['settings']['api']);
        }
      }
      return $response;
    }
  }
}