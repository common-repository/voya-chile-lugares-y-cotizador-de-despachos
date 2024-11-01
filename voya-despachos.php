<?php
/*
 * Plugin Name: Voyapp - Lugares y Cotizador de Despachos
 * Plugin URI: https://voyapp.cl
 * Description: Añade los estados y ciudades de Chile y México a WooCommerce. También podrás contar con un cotizador de despachos de múltiples couriers y mucho más.
 * Version: 1.7.4
 * Author: Onion Media
 * Author URI: https://onionmedia.cl
 * Requires PHP: 7.0
 * Requires at least: 5.6
 * Tested up to: 6.6
 * WC requires at least: 4.5
 * WC tested up to: 9.3
 */

if(!defined( 'ABSPATH' )) exit;

/**
 * Check if WooCommerce is active
 */
if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
  define( 'VOYA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
  define( 'VOYA_PLUGIN_URL', plugin_dir_url(__FILE__) );
  define( 'VOYA_URL', "https://app.voyapp.cl/" );
  define( 'VOYA_URL_FRONTOFFICE', "https://voyapp.cl/" );
  define( 'VOYA_APP_NAME', "Voyapp" );
  define( 'VOYA_PLUGIN_SLUG', 'voyapp-despachos');
  define( 'VOYA_SUPPORTED_COUNTRIES', ['CL','MX']);
  define( 'VOYA_CALL_TIMEOUT', 15);
  define( 'VOYA_VERSION', '1.7.4');
  define( 'VOYA_ENDPOINTS', 
        [
          'get_rates' => VOYA_URL.'api/v2/rates',
          'get_rates_test_mode' => VOYA_URL.'api/integration',
          'create_shipping_label' => VOYA_URL.'api/labels/shipping/create',
          'tracking' => VOYA_URL.'api/tracking'
        ]
  );
  
  /**
   * Valida si el plugin de MKRapel esta activo. Si lo esta, reemplaza su script de selector en checkout. Si no lo esta, añade toda la data de voyapp
   */
  function voyacl_despachos_destinos_init(){
    require_once (VOYA_PLUGIN_PATH . '/includes/classes/voya_despachos_destinos.php');
    if(!in_array('wc-ciudades-y-regiones-de-chile/wc-ciudades-y-regiones-de-chile.php', apply_filters('active_plugins', get_option('active_plugins')))){
      global $pagenow;
      $GLOBALS['wc_states_places'] = new VoyaDespachosDestinos(__FILE__);
    }else{
      $voyaOverwriteJS = new VoyaDespachosDestinos(__FILE__, true);
    }
  }

  function voyacl_despachos_calculo_init(){
      require_once (VOYA_PLUGIN_PATH . '/includes/classes/voya_despachos_calculo.php');
      $abdc = new Voya_Despachos_Calculo();
  }

  function voyacl_despachos_tracking(){
      require_once (VOYA_PLUGIN_PATH . '/includes/classes/voya_despachos_tracking.php');
      $vsc = new VoyaDespachosTracking();
  }

  function voyacl_despachos_notification(){
      require_once (VOYA_PLUGIN_PATH . '/includes/classes/voya_despachos_notification.php');
      $vpn = new VoyaDespachosNotification();
  }

  function voyacl_despachos_endpoints(){
    require_once (VOYA_PLUGIN_PATH . '/includes/classes/voya_despachos_endpoints.php');
    $vde = new VoyaDespachosEndpoints();
  }

  function voyacl_despachos_third_level_address(){
    require_once (VOYA_PLUGIN_PATH . '/includes/classes/voya_despachos_third_level_address.php');
    $vtla = new VoyaDespachosThirdLevelAddress();
  }

  function voyacl_despachos_menus(){
    require_once (VOYA_PLUGIN_PATH . '/includes/classes/voya_despachos_menus.php');
    $vdm = new VoyaDespachosMenus();
  }

  function voyacl_despachos_wc_order(){
    require_once (VOYA_PLUGIN_PATH . '/includes/classes/voya_despachos_wc_order.php');
    $vdwo = new VoyaDespachosWCOrder();
  }

  function voyacl_despachos_calculo($methods){
      $methods[] = 'Voya_Despachos_Calculo';
      return $methods;
  }
  
  function voyacl_settings_link( $links ) {
    $links[] = '<a href="' .
      admin_url( 'admin.php?page=wc-settings&tab=shipping&section=voya_despachos' ) .
      '">' . __('Settings') . '</a>';
    return $links;
  }

  function voyacl_custom_shipping_method_label( $label, $method ){
    if(strpos($method->method_id, 'voya_despachos') !== false) {
      $options = get_option('woocommerce_voya_despachos_settings',[]);
      $displayCourierLogos = 0;
      $imageUri = "";
      if(isset($options['display_couriers_logos']) && $options['display_couriers_logos'] >= 1){
        $displayCourierLogos = $options['display_couriers_logos'];
        $path = VOYA_PLUGIN_PATH .'/public/assets/couriers_logos/'.$method->meta_data["image"];
        $fallbackPath = VOYA_PLUGIN_PATH .'/public/assets/couriers_logos/default.png';
        if(file_exists($path)){
          $imageUri = VOYA_PLUGIN_URL.'/public/assets/couriers_logos/'.$method->meta_data["image"];
        }elseif (file_exists($fallbackPath)) {
          $imageUri = VOYA_PLUGIN_URL.'/public/assets/couriers_logos/default.png';
        }else{
          $displayCourierLogos = 0;
        }
      }
      $newLabel = "";
      $explodedLabel = explode(":",$label);
      if($displayCourierLogos == 0){ //No mostrar logos
        if(array_key_exists("courier_titulo_alternativo",$method->meta_data) && $method->meta_data["courier_titulo_alternativo"] != ""){
          $explodedLabel[0] = $method->meta_data["courier_titulo_alternativo"];
          $newLabel = $explodedLabel[0];
          if(isset($explodedLabel[1])){
            $newLabel .= ':'.$explodedLabel[1];
          }
        }
        if(array_key_exists("dias_despacho",$method->meta_data) && $method->meta_data["dias_despacho"] != "N/D"){
          $newLabel = $explodedLabel[0].' - '.$method->meta_data["dias_despacho"].':'.$explodedLabel[1];
        }
      }else{
        $alt = 0;
        $days = 0;
        if(array_key_exists("dias_despacho",$method->meta_data) && $method->meta_data["dias_despacho"] != "N/D"){
          $days = 1;
        }
        if(array_key_exists("courier_titulo_alternativo",$method->meta_data) && $method->meta_data["courier_titulo_alternativo"] != ""){
          $alt = 1;
        }
        if($displayCourierLogos == 1 && $alt == 0){ //"Mostrar logos solo para modalidades sin nombre personalizado" y "el nombre no es personalizado"
          $deliveryMode = $explodedLabel[0];
          $deliveryMode = explode(')', (explode('(', $deliveryMode)[1]))[0];
          $newLabel = '<img decoding="async" src="'.$imageUri.'" style="display:inline;max-width: 90px;vertical-align: middle;" />'
          .$explodedLabel[1].'<br><p style="line-height: 80%; margin-bottom: 10px;margin-top: 0px;"><small><span><b>Servicio:</b> '.$deliveryMode.'</span>';
          if($days){
            $newLabel .= '<br><span><b>Entrega:</b> '.$method->meta_data["dias_despacho"].'</span>';
          }
          $newLabel .= '</small></p>';
        }elseif ($displayCourierLogos == 1 && $alt == 1) { //"Mostrar logos solo para modalidades sin nombre personalizado" y "el nombre si es personalizado"
          $explodedLabel[0] = $method->meta_data["courier_titulo_alternativo"];
          $newLabel = $explodedLabel[0];
          if(isset($explodedLabel[1])){
            $newLabel .= ':'.$explodedLabel[1];
          }
          if($days){
            $newLabel = $explodedLabel[0].' - '.$method->meta_data["dias_despacho"];
            if(isset($explodedLabel[1])){
              $newLabel .= ':'.$explodedLabel[1];
            }
          }
        }elseif ($displayCourierLogos == 2){ // "Mostrar logos para todas las modalidades"
          if($alt == 0){
            $deliveryMode = $explodedLabel[0];
            $deliveryMode = explode(')', (explode('(', $deliveryMode)[1]))[0];
          }else{
            $deliveryMode = $method->meta_data["courier_titulo_alternativo"];
          }
          $explodedDisplay = '';
          if(isset($explodedLabel[1])){
            $explodedDisplay = $explodedLabel[1];
          }
          
          $newLabel = '<img decoding="async" src="'.$imageUri.'" style="display:inline;max-width: 90px;vertical-align: middle;" />'
          .$explodedDisplay.'<br><p style="line-height: 80%; margin-bottom: 10px;margin-top: 0px;"><small><span><b>Servicio:</b> '.$deliveryMode.'</span>';
          if($days){
            $newLabel .= '<br><span><b>Entrega:</b> '.$method->meta_data["dias_despacho"].'</span>';
          }
          $newLabel .= '</small></p>';
        }
      }
      return $newLabel ? $newLabel : $label;
    }
    return $label;
  }

  function voyacl_order_shipping_to_display_shipped_via( $shipped_via, $order ) {
    $shippingData = $order->get_shipping_methods();
    $altTitle = null;
    foreach ($shippingData as $objectId => $singleObject) {
      if($singleObject->get_meta('courier_titulo_alternativo')){
        $altTitle = $singleObject->get_meta('courier_titulo_alternativo');
        break;
      }
    }
    if($altTitle){
      $shipped_via = '&nbsp;<small class="shipped_via">' . sprintf( __( 'vía %s', 'woocommerce' ), $altTitle) . '</small>';
    }
    return $shipped_via;
  }

  function voyacl_checkout_table_credits() {
    $options = get_option('woocommerce_voya_despachos_settings',[]);
    if(isset($options['display_powered_by_checkout']) && $options['display_powered_by_checkout'] == 'yes'){
      echo '<tr><td colspan="2" style="text-align: right;font-size: 9px;border:0;">Cotización de despachos por <a href="'.VOYA_URL_FRONTOFFICE.'" target="_blank">'.VOYA_APP_NAME.'.</a></td></tr><style>.woocommerce table.shop_table {border:0;}</style>';
    }
  }

  if ( is_admin() ) {
    voyacl_despachos_menus();
    voyacl_despachos_wc_order();
  }

  voyacl_despachos_notification();
  voyacl_despachos_third_level_address();
  voyacl_despachos_endpoints();
  add_action('plugins_loaded','voyacl_despachos_destinos_init',1);
  add_action('plugins_loaded','voyacl_despachos_tracking',1);
  add_action('woocommerce_shipping_init', 'voyacl_despachos_calculo_init');
  add_action('woocommerce_review_order_after_order_total', 'voyacl_checkout_table_credits' );
  add_filter('woocommerce_cart_shipping_method_full_label', 'voyacl_custom_shipping_method_label', 10, 2);
  add_filter('woocommerce_shipping_methods', 'voyacl_despachos_calculo');
  add_filter('woocommerce_order_shipping_to_display_shipped_via', 'voyacl_order_shipping_to_display_shipped_via', 10, 2 );
  add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'voyacl_settings_link');

  add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
      // Declarar compatibilidad con HPOS
      \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
      // Declarar compatibilidad con checkout blocks
      \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
    }
    try {
      if (version_compare(get_option('woocommerce_version'), '8.0', '>=')) {
        if ( class_exists( \Automattic\WooCommerce\Utilities\OrderUtil::class ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
          define( 'VOYA_WC_HPOS', true);
        }else{
          define( 'VOYA_WC_HPOS', false);
        }
      } else {
        define( 'VOYA_WC_HPOS', false);
      }
    } catch (\Throwable $th) {
      define( 'VOYA_WC_HPOS', false);
    }
  });
};