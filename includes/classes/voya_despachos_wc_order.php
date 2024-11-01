<?php
if (!class_exists('VoyaDespachosWCOrder')) {
  class VoyaDespachosWCOrder {
    
    function __construct() {
        add_action( 'add_meta_boxes', [$this, 'voyaclAddMetaBoxes']);
        add_action( 'wp_ajax_'.VOYA_PLUGIN_SLUG.'_slt_get_action', [$this, 'slt_get_data_ajax'] );
        add_action( 'wp_ajax_'.VOYA_PLUGIN_SLUG.'_sl_create_action', [$this, 'slt_create_ajax'] );
    }

    function voyaclAddMetaBoxes()
    {
      $screen = '';
      if(VOYA_WC_HPOS){
        $screen = wc_get_page_screen_id('shop-order');
      }else{
        $screen = 'shop_order';
      }
      add_meta_box( VOYA_PLUGIN_SLUG.'wc-metabox', VOYA_APP_NAME, [$this, 'voyaclAddPlatformMetaboxContent'], $screen, 'side', 'core' );
    }

    function voyaclAddPlatformMetaboxContent(){
      
      if(VOYA_WC_HPOS){
        $WCOrder = wc_get_order( get_the_ID() );
        $orderId = $WCOrder->get_id();
      }else{
        global $post;
        $orderId = $post->ID;
      }
      if ( ! class_exists( 'VoyaDespachosShippingLabelCreateForm' ) ) {
        require_once VOYA_PLUGIN_PATH . '/includes/classes/forms/voya_despachos_shipping_label_create_form.php';
      }
      add_thickbox();
      $createShippingLabel = new VoyaDespachosShippingLabelCreateForm();
      $createShippingLabel->display_page($orderId);
    }
    
    //--AJAX REQUESTS
    function slt_get_data_ajax() {
      $sltid = $_POST['sltID'];
      $sltData = get_post_meta($sltid);
      $responseData = array();
      $responseData['remitente_nombre'] = '';
      $responseData['remitente_rut'] = '';
      $responseData['remitente_telefono'] = '';
      $responseData['remitente_email'] = '';
      $responseData['remitente_region'] = '';
      $responseData['remitente_comuna'] = '';
      $responseData['remitente_direccion'] = '';
      if(isset($sltData['remitente_nombre']) && isset($sltData['remitente_nombre'][0]) && !empty($sltData['remitente_nombre'][0])){
        $responseData['remitente_nombre'] = $sltData['remitente_nombre'][0];
      }
      if(isset($sltData['remitente_rut']) && isset($sltData['remitente_rut'][0]) && !empty($sltData['remitente_rut'][0])){
        $responseData['remitente_rut'] = $sltData['remitente_rut'][0];
      }
      if(isset($sltData['remitente_telefono']) && isset($sltData['remitente_telefono'][0]) && !empty($sltData['remitente_telefono'][0])){
        $responseData['remitente_telefono'] = $sltData['remitente_telefono'][0];
      }
      if(isset($sltData['remitente_email']) && isset($sltData['remitente_email'][0]) && !empty($sltData['remitente_email'][0])){
        $responseData['remitente_email'] = $sltData['remitente_email'][0];
      }
      if(isset($sltData['remitente_region']) && isset($sltData['remitente_region'][0]) && !empty($sltData['remitente_region'][0])){
        $responseData['remitente_region'] = $sltData['remitente_region'][0];
      }
      if(isset($sltData['remitente_comuna']) && isset($sltData['remitente_comuna'][0]) && !empty($sltData['remitente_comuna'][0])){
        $responseData['remitente_comuna'] = $sltData['remitente_comuna'][0];
      }
      if(isset($sltData['remitente_direccion']) && isset($sltData['remitente_direccion'][0]) && !empty($sltData['remitente_direccion'][0])){
        $responseData['remitente_direccion'] = $sltData['remitente_direccion'][0];
      }
      echo json_encode($responseData);
      die;
    }

    function slt_create_ajax() {
      try {
        $responseData = array();
        $options = get_option('woocommerce_voya_despachos_settings',[]);
        $apikey = '';
        if( isset($options['api']) && !empty($options['api']) ){
          $apikey = $options['api'];
        }else{
          $responseData['status'] = 'error';
          $responseData['message'] = 'No se ha establecido una API KEY de '.VOYA_APP_NAME.' para poder acceder a esta función.';
          echo json_encode($responseData);
          die;
        }
        $headers = array();
        $headers["Authorization"] = "Bearer ".$apikey;
        $headers["Accept"] = "application/json";
        $data = array();
        $data["platform"] = "wp_wc";
        foreach ($_POST as $postkey => $postvalue) {
          if(isset($postvalue) && !empty($postvalue)){
            if(strpos($postkey, '__sl__') !== false){
              $data[str_replace('__sl__','',$postkey)] = $postvalue;
            }
          }
        }
        $result = wp_remote_post(VOYA_ENDPOINTS['create_shipping_label'], array(
            'headers' => $headers,
            'method' => 'POST',
            'body' => http_build_query($data),
            'timeout' => VOYA_CALL_TIMEOUT
          )
        );
        if (is_wp_error($result)) {
          $this->write_log("ERROR: IS_WP_ERROR");
          $this->write_log($result);
          $responseData['status'] = 'error';
          $responseData['message'] = 'Error de Wordpress/WooCommerce, por favor ponerse en contacto con el administrador del sitio web.';

          if(isset($result->errors) && isset($result->errors['http_request_failed']) && isset($result->errors['http_request_failed'][0])){
            if(strpos($result->errors['http_request_failed'][0], '28: Operation timed out') !== false){
              $responseData['message'] = 'Se ha agotado el tiempo de espera de la solicitud. Por favor, recargue la página e intente nuevamente.';
            }
          }
          echo json_encode($responseData);
          die;
        }

        $decoded = json_decode($result['body']);
        if (isset($decoded->status) && $decoded->status == 'ERROR') {
          $this->write_log("ERROR ".VOYA_APP_NAME." - MENSAJE INFORMATIVO:");
          $this->write_log($decoded->message);
          $responseData['status'] = 'error';
          $responseData['message'] = $decoded->message;
          echo json_encode($responseData);
          die;
        }

        if(isset($decoded->message) && $decoded->message != "OK"){
          $msg = "";
          switch ($decoded->message) {
            case 'Unauthenticated.':
              $this->write_log("ERROR: PROBLEMA DE AUTENTICACIÓN EN EL SISTEMA ".VOYA_APP_NAME.".");
              $msg = "ERROR: PROBLEMA DE AUTENTICACIÓN EN EL SISTEMA ".VOYA_APP_NAME.".";
              break;
            case 'Too Many Attempts.':
              $this->write_log("ERROR: DEMASIADAS CONSULTAS CONCURRENTES.");
              $msg = "ERROR: DEMASIADAS CONSULTAS CONCURRENTES.";
              break;
            
            default:
                $this->write_log("ERROR: ".$decoded->message);
                $msg = "ERROR: ".$decoded->message;
              break;
          }
          $responseData['status'] = 'error';
          $responseData['message'] = $msg;
          echo json_encode($responseData);
          die;
        }
        
        if(VOYA_WC_HPOS){
          $order = wc_get_order( $_POST['wcorder_id'] );
          $order->update_meta_data( '_voya_shipping_label', $decoded->shipping_label );
          $order->save();
        }else{
          update_post_meta( $_POST['wcorder_id'], '_voya_shipping_label', $decoded->shipping_label);
        }
        $responseData['status'] = 'success';
        $responseData['response'] = $decoded->shipping_label;
        echo json_encode($responseData);
        die;
      } catch (\Throwable $th) {
        $responseData = array();
        $responseData['status'] = 'error';
        $responseData['message'] = 'Ha ocurrido un error inesperado. Por favor, recargue la página e intente nuevamente. Si el error persiste póngase en contacto con nosotros.';
        echo json_encode($responseData);
        die;
      }
    }


    private function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
        error_log( print_r( $log, true ) );
      } else {
        error_log( $log );
      }
    }
  }
}
