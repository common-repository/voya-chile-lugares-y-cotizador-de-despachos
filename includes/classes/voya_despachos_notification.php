<?php
if (!class_exists('VoyaDespachosNotification')) {
  class VoyaDespachosNotification {
    const VOYA_AJAX_ACTION_NOTIFICATION = 'voyacl_dismiss_notification';


    function __construct() {
      add_action( 'admin_notices', [$this, 'voyacl_notification']);
      add_action( 'wp_ajax_'.self::VOYA_AJAX_ACTION_NOTIFICATION, [$this,'voyacl_dismiss_notification' ]);
      add_action( 'rest_api_init', function () {
        register_rest_route( 'voyacl/v1', '/notifications', array(
          'methods' => 'POST',
          'callback' => [$this,'voyacl_notification_listener'],
          'permission_callback' => '__return_true'
        ) );
      });
    }

    function voyacl_notification() {
      $options = get_option('woocommerce_voya_despachos_settings',[]);
      if(isset( $options['admin_notice']) &&
        isset($options['admin_notice']['type']) && !empty($options['admin_notice']['type'])
        ){
        switch ($options['admin_notice']['type']) {
          case 1:
            // Notificación inicial
            if(isset($options['admin_notice']['url']) && !empty($options['admin_notice']['url'])){
              echo '<div class="notice notice-info" id="voyacl_admin_notice" style="display: inline-flex; width: 98%;">
                <div style="width: 150px;height: 150px;margin-right: 20px;position: relative;">
                  <img style="max-height: 100%;max-width: 100%;position: absolute;top: 0;bottom: 0;left: 0;right: 0;margin: auto;"
                  src="'.VOYA_PLUGIN_URL.'/public/assets/plugin_logo.png" width="125"/>
                </div>
                <div>
                  <h3>'.VOYA_APP_NAME.' - Cotizador de Despachos</h3>
                  <p><i class="fa fa-search fa-spin"></i>Se ha creado una orden de compra para tu dominio. Para realizar el pago solo debes presionar el botón "Ir a Pagar" y serás redirigido automáticamente a nuestra plataforma.</p>
                  <p style="margin-top:10px;"><a class="button-secondary" id="voyacl_dismiss_notification_btn">Ocultar notificación</a>  <a style="margin-left:10px;" class="button-primary" href="'.$options['admin_notice']['url'].'" target="_blank">Ir a Pagar</a></p>
                </div>
              </div>';
            }
            break;
          case 2:
            // Recordatorio
            if(isset($options['admin_notice']['url']) && !empty($options['admin_notice']['url'])){
              echo '<div class="notice notice-warning" id="voyacl_admin_notice" style="display: inline-flex; width: 98%;">
                <div style="width: 150px;height: 150px;margin-right: 20px;position: relative;">
                  <img style="max-height: 100%;max-width: 100%;position: absolute;top: 0;bottom: 0;left: 0;right: 0;margin: auto;"
                  src="'.VOYA_PLUGIN_URL.'/public/assets/plugin_logo.png" width="125"/>
                </div>
                <div>
                  <h3>'.VOYA_APP_NAME.' - Cotizador de Despachos</h3>
                  <p>Recuerda realizar el pago de la orden de compra de tu dominio y así evitar la suspensión del servicio. Para pagar, solo debes presionar el botón "Ir a Pagar" y serás redirigido automáticamente a nuestra plataforma.</p>
                  <p style="margin-top:10px;"><a class="button-secondary" id="voyacl_dismiss_notification_btn">Ocultar notificación</a>  <a style="margin-left:10px;" class="button-primary" href="'.$options['admin_notice']['url'].'" target="_blank">Ir a Pagar</a></p>
                </div>
              </div>';
            }
            break;
          case 3:
            // Suspension de servicio
            if(isset($options['admin_notice']['url']) && !empty($options['admin_notice']['url'])){
              echo '<div class="notice notice-error" id="voyacl_admin_notice" style="display: inline-flex; width: 98%;">
                <div style="width: 150px;height: 150px;margin-right: 20px;position: relative;">
                  <img style="max-height: 100%;max-width: 100%;position: absolute;top: 0;bottom: 0;left: 0;right: 0;margin: auto;"
                  src="'.VOYA_PLUGIN_URL.'/public/assets/plugin_logo.png" width="125"/>
                </div>
                <div>
                  <h3>'.VOYA_APP_NAME.' - Cotizador de Despachos</h3>
                  <p>Lamentamos informarte que el servicio '.VOYA_APP_NAME.' ha sido suspendido por no pago, por ende, tu tienda no podrá realizar cotizaciones de despachos hasta reactivar el servicio. ¡Pero no te preocupes, reactivarlo es muy simple! Solo debes realizar el pago de la orden de compra del dominio. Para hacerlo, debes presionar el botón "Ir a Pagar" y serás redirigido automáticamente a nuestra plataforma.</p>
                  <p style="margin-top:10px;"><a class="button-secondary" id="voyacl_dismiss_notification_btn">Ocultar notificación</a>  <a style="margin-left:10px;" class="button-primary" href="'.$options['admin_notice']['url'].'" target="_blank">Ir a Pagar</a></p>
                </div>
              </div>';
            }
            break;
          case 98:
            // Notificacion personalizada
            if(
              isset($options['admin_notice']['message']) && !empty($options['admin_notice']['message']) &&
              isset($options['admin_notice']['status']) && !empty($options['admin_notice']['status'])
              ){
              echo '<div class="notice notice-'.$options['admin_notice']['status'].'" id="voyacl_admin_notice" style="display: inline-flex; width: 98%;">
                  <div style="width: 150px;height: 150px;margin-right: 20px;position: relative;">
                    <img style="max-height: 100%;max-width: 100%;position: absolute;top: 0;bottom: 0;left: 0;right: 0;margin: auto;"
                    src="'.VOYA_PLUGIN_URL.'/public/assets/plugin_logo.png" width="125"/>
                  </div>
                  <div>
                    <h3>'.VOYA_APP_NAME.' - Cotizador de Despachos</h3>
                      <p>'.$options['admin_notice']['message'].'</p>
                      <p style="margin-top:10px;"><a class="button-secondary" id="voyacl_dismiss_notification_btn">Ocultar notificación</a> </p>
                  </div>
                </div>';
            }
              break;
          case 99:
            // Actualización de plugin
            echo '<div class="notice notice-success" id="voyacl_admin_notice" style="display: inline-flex; width: 98%;">
                <div style="width: 150px;height: 150px;margin-right: 20px;position: relative;">
                  <img style="max-height: 100%;max-width: 100%;position: absolute;top: 0;bottom: 0;left: 0;right: 0;margin: auto;"
                  src="'.VOYA_PLUGIN_URL.'/public/assets/plugin_logo.png" width="125"/>
                </div>
                <div>
                  <h3>'.VOYA_APP_NAME.' - Cotizador de Despachos</h3>
                    <p><b>¡Buenas noticias!</b> Hemos subido una nueva versión del plugin '.VOYA_APP_NAME.'. Te recomendamos actualizarlo a la brevedad, ya que al hacerlo, podrás usar todas las nuevas funciones que hemos creado para ti.</p>
                    <p style="margin-top:10px;"><a class="button-secondary" id="voyacl_dismiss_notification_btn">Ocultar notificación</a>  <a style="margin-left:10px;" class="button-primary" href="'.admin_url( 'plugins.php' ).'">Ir a sección de Plugins</a></p>
                </div>
              </div>';
            break;
          default:
            //Default none
            break;
        }

        wp_enqueue_script('voya-dismiss-notification-js', VOYA_PLUGIN_URL.'/public/js/voya-dismiss-notification.js', array('jquery'), VOYA_VERSION, true);
        wp_localize_script( 'voya-dismiss-notification-js', 'ajaxParams',
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php'),
                'action'    => self::VOYA_AJAX_ACTION_NOTIFICATION
            )
        );
      }
    }

    function voyacl_enqueue_payment_notification_assets(){
      wp_enqueue_script('voya-dismiss-notification-js', VOYA_PLUGIN_URL.'/public/js/voya-dismiss-notification.js', array('jquery'), VOYA_VERSION, true);
    }
    
    function voyacl_dismiss_notification() {
      $options = get_option('woocommerce_voya_despachos_settings',[]);
      $notificationData = [];  
      $options['admin_notice'] = $notificationData;
      update_option('woocommerce_voya_despachos_settings',$options);
      echo json_encode(["status" => 1]);
      die;
    }
    
    function voyacl_notification_listener( $data ) {
      $response = ["status" => 0];
      if(
        isset($data['apikey']) && !empty($data['apikey']) &&
        isset($data['type']) && !empty($data['type'])
        ){
        $options = get_option('woocommerce_voya_despachos_settings',[]);
        if(isset($options['api']) && $options['api'] != '' && $options['api'] == $data['apikey']){
          $notificationData = [];
          //Notificaciones de pago
          if($data['type'] >= 1 && $data['type'] <= 3 &&
            isset($data['url']) && !empty($data['url']) && wp_is_uuid($data['url'])
            ){
            $notificationData['url'] = VOYA_URL.'pagos/oc/'.$data['url'];
            $notificationData['type'] = $data['type'];
            $response["status"] = 1;
          }
          //Personalizada
          elseif($data['type'] == 98 && isset($data['message']) && isset($data['status']) && in_array($data['status'],['success','warning','error'])){
            $notificationData['type'] = $data['type'];
            $notificationData['message'] = wp_strip_all_tags($data['message']);
            $notificationData['status'] = $data['status']; //[success|warning|error]
            $response['status'] = 1;
          }
          //Actualizacion
          elseif($data['type'] == 99){
            $notificationData['type'] = $data['type'];
            $response['status'] = 1;
          }
          //Eliminado
          elseif($data['type'] == 100){
            $response['status'] = 1;
          }
          $options['admin_notice'] = $notificationData;
          update_option('woocommerce_voya_despachos_settings',$options);
        }
      }
      return $response;
    }
  }
}