<?php
if (!class_exists('VoyaDespachosTracking')) {
  class VoyaDespachosTracking {
    const AJAX_ACTION = 'voya_seguimiento_pedido'; // ajax action
    const DOM_TARGET =  'voya_tracking_response'; // dom element to put the quotes
    private $options = [];
    function __construct() {
      $this->options = get_option('woocommerce_voya_despachos_settings',[]);
      add_shortcode('voya_seguimiento', array($this, 'addShortcode'));
      add_shortcode('voyapp_tracking', array($this, 'addShortcode'));
      add_action( 'wp_ajax_'.self::AJAX_ACTION, array($this, 'getTracking'));
      add_action( 'wp_ajax_nopriv_'.self::AJAX_ACTION, array($this, 'getTracking'));
    }

    /**
     * Shortcode callback
     * @param $atts
     * @return string
     */
    public function addShortcode($atts){
      $out = '';
      
      $storeCountry = wc_get_base_location()['country'];
      if( file_exists(VOYA_PLUGIN_PATH . '/includes/data/couriers/tracking/' . $storeCountry . '.php') ){
        include(VOYA_PLUGIN_PATH . '/includes/data/couriers/tracking/' . $storeCountry . '.php');
      }else{
        include(VOYA_PLUGIN_PATH . '/includes/data/couriers/tracking/CL.php');
      }

      $couriersToDisplay = [];
      if(isset($this->options['tracking_couriers']) && !empty($this->options['tracking_couriers']) && is_array($this->options['tracking_couriers']) && count($this->options['tracking_couriers']) > 0){
        $input = '';
        if(count($this->options['tracking_couriers']) > 1){
          $input = '<label for="voya_courier">Courier:</label>
            <select id="voya_courier" name="courier">';
          foreach ($this->options['tracking_couriers'] as $singleCourier) {
            $input .= '<option value="'.$singleCourier.'">'.$mapCouriersCountries[$singleCourier].'</option>';
          }
          $input .= '</select>';
        }else{
          $input = '<input type="hidden" id="voya_courier" name="courier" value="'.$this->options['tracking_couriers'][0].'">';
        }
        
        $out = '
          <style>

            ul.voya-list ul, li.voya-list{
              list-style: none;
              padding: 0;
            }

            .voya-timeline-container{
              display: flex;
              justify-content: center;
              align-items: center;
              padding: 0 1rem;
              padding: 3rem 0;
            }
            .voya-wrapper{
              background: #eaf6ff;
              padding: 2rem;
              border-radius: 15px;
            }
            h1.voya-heading{
              font-size: 1.1rem;
              font-family: sans-serif;
              line-height: 1.5
            }
            .voya-sessions{
              margin-top: 2rem;
              border-radius: 12px;
              position: relative;
            }
            li.voya-list{
              padding-bottom: 1.5rem;
              border-left: 1px solid #4274FF;
              position: relative;
              padding-left: 20px;
              margin-left: 10px;
            }
            li.voya-list:last-child{
              border: 1px solid transparent;
              padding-bottom: 0;
            }
            li.voya-list:before{
              content: "";
              width: 15px;
              height: 15px;
              background: #4274FF;
              border: 1px solid #4274FF;
              border-radius: 50%;
              position: absolute;
              left: -8px;
              top: 0;
            }
            .voya-time{
              color: #2a2839;
              font-family: "Poppins", sans-serif;
              font-weight: 500;
            }
            li.voya-list p{
              color: #4f4f4f;
              font-family: sans-serif;
              line-height: 1.5;
              margin-top:0.4rem;
            }
            /*--------------FORM*/
              .voya-form-inline input[type=text], .voya-form-inline select {
                width: 100%;
                padding: 12px 20px;
                margin: 8px 0;
                display: block;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
              }

              .voya-form-inline button[type=button] {
                width: 100%;
                background-color: #4274FF;
                color: white;
                padding: 14px 20px;
                margin: 8px 0;
                margin-top: 25px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-weight: bold;
                -webkit-transition: .5s all;   
                -webkit-transition-delay: 5s; 
                -moz-transition: .5s all;   
                -moz-transition-delay: 5s; 
                -ms-transition: .5s all;   
                -ms-transition-delay: 5s; 
                -o-transition: .5s all;   
                -o-transition-delay: 5s; 
                transition: .5s all;   
                transition-delay: 5s; 
              }

              .voya-form-inline button[type=button]:hover {
                background-image: linear-gradient(140deg, #4274FF 0%, #0EC0EA 100%);
                -webkit-transition-delay: .5s;
                -moz-transition-delay: .5s;
                -ms-transition-delay: .5s;
                -o-transition-delay: .5s;
                transition-delay: .5s;
              }
              .voya-form-inline button[type=button]:focus {
                background-image: linear-gradient(140deg, #4274FF 0%, #0EC0EA 100%);
              }

              div.voya-form-container {
                border-radius: 5px;
                background-color: #f2f2f2;
                padding-top: 30px;
                padding-left: 20px;
                padding-right: 20px;
                padding-bottom: 55px;
              }
              .voya-form-container .voya-powered-by{
                font-size: 14px;
                float: right;
              }
              .voya-form-container .voya-powered-by a{
                color: #4274ff;
                text-decoration: underline;
              }
          </style>
          <div id="voya_tracking_error">
          </div>
          <div class="voya-form-container">
            <form class="voya-form-inline" id="voya-form-tracking" action="#">
              <label for="voya_tracking_code">Código de seguimiento:</label>
              <input type="text" id="voya_tracking_code" name="tracking_code">';
        $out .= $input;
        $out .= '<button type="button" id="trackShipment">Buscar Envío</button>
                </form>
                ';
        
        if(isset($this->options['display_powered_by_tracking']) && $this->options['display_powered_by_tracking'] == 'yes'){
          $out .= '<p class="voya-powered-by">
            Seguimiento de pedidos realizado por <a href="'.VOYA_URL_FRONTOFFICE.'" target="_blank">'.VOYA_APP_NAME.'.</a>
          </p>';
        }  
        $out .=   '</div>
                  <div id="voya_tracking_response">
                  </div>
                ';
  
        // loading js
        // jquery depends
        wp_enqueue_script('voya-tracking-js', VOYA_PLUGIN_URL.'/public/js/voya-tracking.js', array('jquery'), VOYA_VERSION,true);
        // passing to js needed vars
        wp_localize_script( 'voya-tracking-js', 'ajaxParams',
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php'), // for frontend ( not admin )
                'action'    => self::AJAX_ACTION, //
            )
        );
        // render shortcode replacement
      }else{
        $out = '<h3>No se ha seleccionado ningún courier. Para hacerlo, debes acceder a las configuraciones de '.VOYA_APP_NAME.'.</h3>';
      }
      return $out;
    }

    /**
     * Ajax Callback
     */
    public function getTracking(){
        echo $this->voyaQueryTracking();
        die();
    }

    /**
     * Getting random Qoute from the file
     * @param $path
     * @return mixed
     */
    public function voyaQueryTracking(){
        $headers = $this->getRequestHeaders();
        if(!$headers){
          return "No se ha establecido una API KEY para poder acceder a esta función.";
        }
        $data = $this->setRemotePostData();
        $result = wp_remote_post(VOYA_ENDPOINTS['tracking'], array(
            'headers' => $headers,
            'method' => 'POST',
            'body' => http_build_query($data),
            'timeout' => VOYA_CALL_TIMEOUT,
            'sslverify' => $this->getSSLVerify(),
          )
        );
        $validatedResponse = $this->responseValidation($result);
        if($validatedResponse["status"] == "error"){
          return $validatedResponse["message"];
        }
        return json_encode($validatedResponse);
    }

    private function setRemotePostData(){
      $data = [];
      $data["platform"] = "wp_wc";
      $valid = true;
      if(isset($_POST["tracking_code"]) && !empty($_POST["tracking_code"])){
        $data["tracking_code"] = $_POST["tracking_code"];
      }else{
        $valid = false;
      }
      if(isset($_POST["courier"]) && !empty($_POST["courier"])){
        $data["courier"] = $_POST["courier"];
      }else{
        $valid = false;
      }
      return $valid ? $data : false;
    }

    private function getRequestHeaders(){
      $apikey = '';
      if(isset($this->options['api'])){
        $apikey = $this->options['api'];
      }else{
        return false;
      }
      $headers = array();
      $headers["Authorization"] = "Bearer ".$apikey;
      $headers["Accept"] = "application/json";
      return $headers;
    }

    private function responseValidation($result){
      if (is_wp_error($result)) {
          $this->write_log("ERROR IS_WP_ERROR");
          $this->write_log($result);
          return ["status" => "error","message" => "Error de Wordpress/WooCommerce, por favor ponerse en contacto con el administrador del sitio web."];
        }

        $decoded = json_decode($result['body']);
        if (isset($decoded->status) && $decoded->status == 'ERROR') {
          $this->write_log("ERROR VOYA - MENSAJE INFORMATIVO:");
          $this->write_log($decoded->message);
          return ["status" => "error","message" => $decoded->message];
        }
        if(isset($decoded->message) && $decoded->message != "OK"){
          $msg = "";
          switch ($decoded->message) {
            case 'Unauthenticated.':
              $this->write_log("ERROR: PROBLEMA DE AUTENTICACIÓN EN EL SISTEMA VOYA.");
              $msg = "ERROR: PROBLEMA DE AUTENTICACIÓN EN EL SISTEMA VOYA.";
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
          return ["status" => "error","message" => $msg];
        }
        return ["status" => "success","response"=>$decoded];
    }

    private function getSSLVerify(){
      if($this->options['ssl_verify_flag'] == 'yes'){
        return true;
      }else{
        return false;
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