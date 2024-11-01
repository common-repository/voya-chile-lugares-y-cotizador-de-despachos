<?php
if (!class_exists('Voya_Despachos_Calculo')) {
  class Voya_Despachos_Calculo extends WC_Shipping_Method{
    private $wunit;
    private $dunit;
    private $store_country;
    private $isSupportedCountry;
    private $available_countries = ['CL' => 'Chile', 'MX' => 'México'];
    private $wcBaseCountryName;

    private $test_mode;
    private $transit_days;
    private $additional_transit_days;
    private $api;
    private $default_weight;
    private $default_height;
    private $default_width;
    private $default_length;
    private $default_percentage;
    private $enable_default_shipping;
    private $free_shipping_threshold;
    private $free_shipping_destination;
    private $free_shipping_combine_criteria;
    private $free_shipping_date_limit;
    private $free_shipping_display_mode;
    private $ignored_cities;
    private $round_up;
    private $shipping_mode_reorder;
    private $ssl_verify_flag;
    private $display_powered_by_checkout;
    private $display_couriers_logos;
    private $tracking_couriers;
    private $display_powered_by_tracking;

    public function __construct(){
      //Weight units: kg,g,lbs,oz
      //Dimension units: m,cm,mm,in,yd
      $this->id = 'voya_despachos';
      $this->method_title = VOYA_APP_NAME;
      $this->method_description = 'Con este plugin tus clientes podrán seleccionar la empresa de despachos a utilizar y pagar de inmediato el valor del despacho. Este plugin es compatible con los destinos de "MkRapel Regiones y Ciudades de Chile para WC".<br>Puedes editar este campo en los <a href="'.admin_url( 'admin.php?page=wc-settings&tab=general' ).'">ajustes de WooCommerce.</a>';
      $this->availability = 'including';
      $this->countries = array(
        'CL', 'MX'
      );
      $this->wunit = get_option("woocommerce_weight_unit");
      $this->dunit = get_option("woocommerce_dimension_unit");
      $this->store_country = wc_get_base_location()['country'];
      $this->isSupportedCountry = array_key_exists($this->store_country, $this->available_countries);
      $this->wcBaseCountryName = WC()->countries->countries[$this->store_country];
      $this->init();
      $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
      $this->test_mode = isset($this->settings['test_mode']) ? $this->settings['test_mode'] : 'no';
      $this->transit_days = isset($this->settings['transit_days']) ? $this->settings['transit_days'] : 'no';
      $this->additional_transit_days = isset($this->settings['additional_transit_days']) ? $this->settings['additional_transit_days'] : 0;
      $this->api = isset($this->settings['api']) ? $this->settings['api'] : __('', 'voya_despachos');
      $this->default_weight = isset($this->settings['default_weight']) ? $this->settings['default_weight'] : __('1', 'voya_despachos');
      $this->default_height = isset($this->settings['default_height']) ? $this->settings['default_height'] : __('10', 'voya_despachos');
      $this->default_width = isset($this->settings['default_width']) ? $this->settings['default_width'] : __('10', 'voya_despachos');
      $this->default_length = isset($this->settings['default_length']) ? $this->settings['default_length'] : __('10', 'voya_despachos');
      $this->default_percentage = isset($this->settings['default_percentage']) ? $this->settings['default_percentage'] : __('0', 'voya_despachos');
      
      $this->enable_default_shipping = isset($this->settings['enable_default_shipping']) ? $this->settings['enable_default_shipping'] : '';
      
      $this->free_shipping_threshold = isset($this->settings['free_shipping_threshold']) ? $this->settings['free_shipping_threshold'] : 0;
      $this->free_shipping_destination = isset($this->settings['free_shipping_destination']) ? $this->settings['free_shipping_destination'] : "";
      $this->free_shipping_combine_criteria = isset($this->settings['free_shipping_combine_criteria']) ? $this->settings['free_shipping_combine_criteria'] : 'no';
      $this->free_shipping_date_limit = isset($this->settings['free_shipping_date_limit']) ? $this->settings['free_shipping_date_limit'] : "";
      $this->free_shipping_display_mode = isset($this->settings['free_shipping_display_mode']) ? $this->settings['free_shipping_display_mode'] : 1;

      $this->ignored_cities = isset($this->settings['ignored_cities']) ? $this->settings['ignored_cities'] : "";
      $this->round_up = isset($this->settings['round_up']) ? $this->settings['round_up'] : 0;
      
      $this->shipping_mode_reorder = isset($this->settings['shipping_mode_reorder']) ? $this->settings['shipping_mode_reorder'] : 1;
      $this->ssl_verify_flag = isset($this->settings['ssl_verify_flag']) ? $this->settings['ssl_verify_flag'] : 'yes';
      $this->display_powered_by_checkout = isset($this->settings['display_powered_by_checkout']) ? $this->settings['display_powered_by_checkout'] : 'no';
      $this->display_couriers_logos = isset($this->settings['display_couriers_logos']) ? $this->settings['display_couriers_logos'] : 0;

      $this->tracking_couriers = isset($this->settings['tracking_couriers']) ? $this->settings['tracking_couriers'] : "";
      $this->display_powered_by_tracking = isset($this->settings['display_powered_by_tracking']) ? $this->settings['display_powered_by_tracking'] : 'yes';
    }
    
    function init(){
      $this->init_form_fields();
      $this->init_settings();
      add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }
    
    function init_form_fields(){
      if($this->isSupportedCountry){
        include( VOYA_PLUGIN_PATH . '/includes/data/couriers/tracking/'.$this->store_country.'.php' );
        include( VOYA_PLUGIN_PATH . '/includes/data/cities/'.$this->store_country.'_VOYA_FS_CITIES.php' );
      }else{
        include( VOYA_PLUGIN_PATH . '/includes/data/cities/CL_VOYA_FS_CITIES.php' );
        include( VOYA_PLUGIN_PATH . '/includes/data/couriers/tracking/CL.php' );
      }
      $this->form_fields = array(
        //Configuraciones generales
        //--Pestaña principal
        'api' => array(
          'title' => __('API KEY', 'voya_despachos'),
          'type' => 'text',
          'description' => __('Todas las consultas de precios, seguimiento de pedidos y creación de etiquetas de despacho, son procesadas a través de nuestros servidores. Regístrate en <a href="'.VOYA_URL.'register" target="_blank"><strong>'.VOYA_APP_NAME.'</strong></a> para obtener una API KEY para tu dominio.</br> Si quieres conocer nuestros planes, visita la página del plugin haciendo <a href="'.VOYA_URL_FRONTOFFICE.'planes" target="_blank">click aquí.</a>', 'voya_despachos'),
          'default' => __('', 'voya_despachos'),
          'class'=> 'voyapp-despachos-principal-principal voyapp-despachos-input',
        ),
        //--Pestaña avanzado
        'ssl_verify_flag' => array(
          'title' => 'Habilitar SSL Verify',
          'type' => 'checkbox',
          'label' => 'Activo',
          'description' => 'Activa esta opción para habilitar el parámetro SSL Verify en las consultas. Se recomienda siempre mantenerlo activo.',
          'default' => 'yes',
          'class'=> 'voyapp-despachos-principal-avanzado voyapp-despachos-input',
        ),


        //Configuraciones Cotizador
        //--Pestaña Principal
        'enabled' => array(
          'title' => __('Activo', 'voya_despachos'),
          'type' => 'checkbox',
          'label' => 'Activo',
          'description' => __('Activar Cotizador de despachos '.VOYA_APP_NAME, 'voya_despachos'),
          'default' => 'yes',
          'class'=> 'voyapp-despachos-cotizador-principal voyapp-despachos-input',
        ),
        'test_mode' => array(
          'title' => __('¿Modo de pruebas?', 'voya_despachos'),
          'type' => 'checkbox',
          'label' => 'Activo',
          'description' => __('Activa esta opción si estás recién desarrollando tu ecommerce o estás realizando las pruebas de integración. Las consultas realizadas bajo esta modalidad no serán consideradas en tu facturación mensual.', 'voya_despachos'),
          'default' => 'no',
          'class'=> 'voyapp-despachos-cotizador-principal voyapp-despachos-input',
        ),
        'default_weight' => array(
          'title' => __('Peso por defecto del producto', 'voya_despachos'),
          'type' => 'decimal',
          'description' => __('Este valor será utilizado en los productos que no tengan un "peso" definido en la tienda. Ingresar valor en <strong>' . $this->wunit . '</strong>.', 'voya_despachos'),
          'default' => __('1', 'voya_despachos'),
          'class'=> 'voyapp-despachos-cotizador-principal voyapp-despachos-input',
        ),
        'default_height' => array(
          'title' => __('Altura por defecto del producto', 'voya_despachos'),
          'type' => 'number',
          'description' => __('Este valor será utilizado en los productos que no tengan una "altura" definida en la tienda. Ingresar valor en <strong>' . $this->dunit . '</strong>.', 'voya_despachos'),
          'default' => __('10', 'voya_despachos'),
          'class'=> 'voyapp-despachos-cotizador-principal voyapp-despachos-input',
        ),
        'default_width' => array(
          'title' => __('Ancho por defecto del producto', 'voya_despachos'),
          'type' => 'number',
          'description' => __('Este valor será utilizado en los productos que no tengan un "ancho" definido en la tienda. Ingresar valor en <strong>' . $this->dunit . '</strong>.', 'voya_despachos'),
          'default' => __('10', 'voya_despachos'),
          'class'=> 'voyapp-despachos-cotizador-principal voyapp-despachos-input',
        ),
        'default_length' => array(
          'title' => __('Largo por defecto del producto', 'voya_despachos'),
          'type' => 'number',
          'description' => __('Este valor será utilizado en los productos que no tengan un "largo" definido en la tienda. Ingresar valor en <strong>' . $this->dunit . '</strong>.', 'voya_despachos'),
          'default' => __('10', 'voya_despachos'),
          'class'=> 'voyapp-despachos-cotizador-principal voyapp-despachos-input',
        ),       
        //--Pestaña Modalidades y precios
        'enable_default_shipping' => array(
          'title' => __('¿Habilitar modalidad de despacho "Envío por pagar"?', 'voya_despachos'),
          'type' => 'checkbox',
          'label' => 'Activo',
          'description' => __('Activa esta opción si deseas incluir la modalidad de despacho "Envío por pagar" en caso de que no exista una respuesta de cotización desde los servidores de los couriers.', 'voya_despachos'),
          'default' => '',
          'class'=> 'voyapp-despachos-cotizador-precios voyapp-despachos-input',
        ),
        'round_up' => array(
          'title' => 'Redondear precios',
          'type' => 'select',
          'options' => [0 => 'No redondear', 10 => 'Redondear a la decena superior más cercana', 100 => 'Redondear a la centena superior más cercana', 1000 => 'Redondear al millar superior más cercano', ],
          'description' => 'Este campo permite redondear el valor del despacho a la decena superior más cercana, centena superior más cercana o millar superior más cercano.',
          'class'=> 'voyapp-despachos-cotizador-precios voyapp-despachos-input',
        ),
        'default_percentage' => array(
          'title' => __('Variación porcentual', 'voya_despachos'),
          'type' => 'number',
          'description' => __('Este campo te permite aumentar/disminuir (en porcentaje) el valor final de los envíos. <br>Ejemplo 1: Si quieres que los precios de los despachos aumenten en un 10%, debes ingresar el valor <b>10</b> en este campo.<br>Ejemplo 2: Si quieres que los precios de los despachos disminuyan en un 5%, debes ingresar el valor <b>-5</b> en este campo.', 'voya_despachos'),
          'default' => __('0', 'voya_despachos'),
          'class'=> 'voyapp-despachos-cotizador-precios voyapp-despachos-input',
        ),
        'shipping_mode_reorder' => array(
          'title' => 'Ordenar modalidades de despacho',
          'description' => 'Selecciona una forma en la que se ordenan las modalidades de despacho en el formulario de finalizar compra.',
          'type' => 'select',
          'options' => [1 => 'Sin orden particular (opción por defecto)', 2 => 'Ordenar todas las modalidades por precio de manera ascendente (menor a mayor).', 3 => 'Ordenar todas las modalidades por precio de manera descendente (mayor a menor).', 4 => 'Mostrar solo la modalidad '.VOYA_APP_NAME.' más económica.', 5 => 'Mostrar solo la modalidad '.VOYA_APP_NAME.' más costosa.'],
          'default' => 1,
          'class'=> 'voyapp-despachos-cotizador-precios voyapp-despachos-input',
        ),
        'transit_days' => array(
          'title' => __('¿Mostrar cantidad aproximada de días que tardará el despacho?', 'voya_despachos'),
          'type' => 'checkbox',
          'label' => 'Activo',
          'description' => 'Activa esta opción si deseas mostrar la cantidad aproximada de días que demora cada courier en realizar la entrega del pedido. <br>Este número es proporcionado directamente por la empresa de despachos. <br>No todos los couriers entregan esta información.<br>',
          'default' => 'no',
          'class'=> 'voyapp-despachos-cotizador-precios voyapp-despachos-input',
        ),
        'additional_transit_days' => array(
          'title' => __('Días adicionales que tardará el despacho', 'voya_despachos'),
          'type' => 'number',
          'label' => 'Activo',
          'description' => 'Ingresa la cantidad de días que deseas sumar al valor que demora cada courier en realizar la entrega del pedido. <br>Por ejemplo, si demoras aproximadamente 1 día en preparar un pedido antes de ser despachado, deberías agregar un <b>1</b> en este campo.<br>  <b>Activa la opción anterior para poder visualizar esta información en tu tienda.</b>',
          'default' => 0,
          'class'=> 'voyapp-despachos-cotizador-precios voyapp-despachos-input',
        ),
        //--Pestaña Envio gratis
        'free_shipping_threshold' => array(
          'title' => 'Envío gratis según total de compra del cliente',
          'type' => 'number',
          'description' => 'Este campo permite habilitar la modalidad de despacho "Envío Gratis" cuando el precio total del pedido de un comprador es igual o mayor al valor especificado en este campo. Si no quieres habilitar el envío gratis mediante esta modalidad, deja este campo vacío.',
          'default' => __('0', 'voya_despachos'),
          'class'=> 'voyapp-despachos-cotizador-gratis voyapp-despachos-input',
        ),
        'free_shipping_destination' => array(
          'title' => 'Envío gratis según destino de envío del cliente',
          'type' => 'multiselect',
          'options' => $fsCities,
          'description' => 'Este campo permite habilitar la modalidad de despacho "Envío Gratis" cuando la comuna de despacho seleccionada por el comprador se encuentra presente en esta lista. Si no quieres habilitar el envío gratis mediante esta modalidad, deja este campo vacío. País de la tienda: <b>'.$this->wcBaseCountryName.'</b>',
          'class'=> 'voyapp-despachos-cotizador-gratis voyapp-despachos-input',
        ),
        'free_shipping_combine_criteria' => array(
          'title' => 'Combinar criterios de evaluación para Envío gratis',
          'type' => 'checkbox',
          'label' => 'Activo',
          'description' => 'Activa esta opción para combinar los criterios de evaluación de Envío Gratis de las dos opciones anteriores. Al activar esta opción se evaluará el despacho gratis según <b>"Total de compra" Y "Destino de envío"</b>. 
          Por ejemplo: Despacho gratis desde los $30.000 solamente dentro de las comunas de Providencia y Macul.',
          'default' => 'no',
          'class'=> 'voyapp-despachos-cotizador-gratis voyapp-despachos-input',
        ), 
        'free_shipping_date_limit' => array(
          'title' => 'Fecha de finalización de "Envío Gratis"',
          'type' => 'text',
          'css' => 'background-color: white;',
          'description' => 'Este campo permite establecer la fecha de finalización del "Envío Gratis" de las 3 opciones anteriores. El "Envío Gratis" se desactivará a las 23:59 de la fecha seleccionada.<br>Si no quieres establecer una fecha límite, deja este campo vacío. Esta opción utiliza la zona horaria establecida en la sección de WordPress "Ajustes" -> "Generales" -> "Zona horaria".',
          'default' => '',
          'class'=> 'voyapp-despachos-cotizador-gratis voyapp-despachos-input',
        ), 
        'free_shipping_display_mode' => array(
          'title' => 'Forma de mostrar la opción "Envío Gratis"',
          'type' => 'select',
          'options' => [1 => 'Mostrar la opción "Envío Gratis" como primera opción preseleccionada y no mostrar otras modalidades cotizadas por '.VOYA_APP_NAME.' (comportamiento por defecto)', 2 => 'Mostrar la opción "Envío Gratis" como primera opción preseleccionada y también mostrar las otras modalidades cotizadas por '.VOYA_APP_NAME, 3 => 'Mostrar la opción "Envío Gratis" como única opción.'],
          'description' => 'Este campo permite establecer la forma en la que se mostrará la opción "Envío Gratis" en la lista de opciones de despacho cuando se cumplen las condiciones para mostrar esta modalidad.',
          'default' => '',
          'class'=> 'voyapp-despachos-cotizador-gratis voyapp-despachos-input',
        ),
        //--Pestaña Miscelaneo
        'ignored_cities' => array(
          'title' => 'Ignorar ciudades/comunas',
          'type' => 'multiselect',
          'options' => $fsCities,
          'description' => 'Este campo permite seleccionar las ciudades/comunas para las cuales '.VOYA_APP_NAME.' no realizará una cotización de precio de despacho. País de la tienda: <b>'.$this->wcBaseCountryName.'</b>'. ( !$this->isSupportedCountry ? ' ('.VOYA_APP_NAME.' no tiene cobertura para este país, mostrando opciones por defecto de Chile).' : ''),
          'class'=> 'voyapp-despachos-cotizador-misc voyapp-despachos-input',
        ),
        'display_couriers_logos' => array(
          'title' => 'Mostrar logos de couriers',
          'type' => 'select',
          'options' => [0 => 'No mostrar logos', 1 => 'Mostrar logos solo a modalidades que no tienen nombre personalizado', 2 => 'Mostrar logos para todas las modalidades' ],
          'description' => 'Este campo permite mostrar los logos de los couriers en la página de finalizar compra.',
          'class'=> 'voyapp-despachos-cotizador-misc voyapp-despachos-input',
        ),
        'display_powered_by_checkout' => array(
          'title' => 'Mostrar créditos de cálculo de precios de despacho en checkout',
          'type' => 'checkbox',
          'label' => 'Activo',
          'description' => 'Activa esta opción para mostrar el mensaje <b><i>"Cotización de despachos por '.VOYA_APP_NAME.'"</i></b> en la página de checkout. Este es un mensaje no invasivo y no interfiere en la compra del cliente. ¡Ayúdanos a seguir creciendo!',
          'default' => 'no',
          'class'=> 'voyapp-despachos-cotizador-misc voyapp-despachos-input',
        ), 
        //Configuraciones Seguimiento
        //--Pestaña Principal
        'tracking_couriers' => array(
          'title' => 'Couriers a mostrar en formulario de seguimiento',
          'type' => 'multiselect',
          'options' => $mapCouriersCountries,
          'description' => 'Este campo permite seleccionar los couriers a mostrar en el formulario de "Seguimiento de pedido"'. ( !$this->isSupportedCountry ? '  País de la tienda: <b>'.$this->wcBaseCountryName.'</b>('.VOYA_APP_NAME.' no tiene cobertura para este país, mostrando opciones por defecto de Chile).' : ''),
          'class'=> 'voyapp-despachos-seguimiento-principal voyapp-despachos-input',
        ),
        'display_powered_by_tracking' => array(
          'title' => 'Mostrar créditos en formulario de seguimiento',
          'type' => 'checkbox',
          'label' => 'Activo',
          'description' => 'Activa esta opción para mostrar el mensaje <b><i>"Seguimiento de pedidos realizado por '.VOYA_APP_NAME.'"</i></b> en el formulario de seguimiento. Este es un mensaje no invasivo y no interfiere en la compra del cliente. ¡Ayúdanos a seguir creciendo!',
          'default' => 'yes',
          'class'=> 'voyapp-despachos-seguimiento-misc voyapp-despachos-input',
        ),
      );
    }
    
    function admin_options() {
      wp_enqueue_script('voya-main-settings-js', VOYA_PLUGIN_URL.'/public/js/main-settings.js', array('jquery'),VOYA_VERSION,true);
      wp_enqueue_style('voya-main-settings-css', VOYA_PLUGIN_URL.'/public/css/main-settings.css', array(), VOYA_VERSION);
      $additionalDescription = '';
      if(!$this->isSupportedCountry){
        $additionalDescription = '<b>Importante:</b> Actualmente, la dirección de tu tienda está configurada en <b>'.$this->wcBaseCountryName.'</b> y '.VOYA_APP_NAME.' no tiene cobertura para este país. Puedes editar este campo en los <a href="'.admin_url( 'admin.php?page=wc-settings&tab=general' ).'">ajustes de WooCommerce.</a> 
        <br>Se mostrarán las configuraciones, opciones y Regiones/Comunas por defecto (Chile).';
      }
      ?>
      <div id="sl-loader" class="blockUI blockOverlay"></div>
      <h2><?php echo VOYA_APP_NAME; ?> - Ajustes</h2>
      <div class="init-hide">
        <?php if($additionalDescription) {?><p> <?php echo $additionalDescription; ?> </p> <?php } ?>
        <nav class="nav-tab-wrapper voyapp-tabs-container voyapp-module-tabs-container">
          <a href="#principal" class="nav-tab voyapp-module-tab nav-tab-active" tabtarget="principal">General</a>
          <a href="#cotizador" class="nav-tab voyapp-module-tab"                tabtarget="cotizador">Cotizador</a>
          <a href="#seguimiento" class="nav-tab voyapp-module-tab"              tabtarget="seguimiento">Seguimiento pedidos</a>
        </nav>

        <!-- Tabs internas -->
        <nav class="nav-tab-wrapper voyapp-tabs-container">
          <a href="#principal-principal" class="nav-tab voyapp-tab voyapp-module-main-tab nav-tab-active hide" module="principal" targets="principal-principal">Integración</a>
          <a href="#principal-avanzado" class=" nav-tab voyapp-tab hide"                                       module="principal" targets="principal-avanzado">Avanzado</a>

          <a href="#cotizador-principal" class="nav-tab voyapp-tab voyapp-module-main-tab hide" module="cotizador" targets="cotizador-principal">Principal</a>
          <a href="#cotizador-precios" class="nav-tab voyapp-tab hide"                      module="cotizador" targets="cotizador-precios">Modalidades y precios</a>
          <a href="#cotizador-gratis" class="nav-tab voyapp-tab hide"                      module="cotizador" targets="cotizador-gratis">Envío gratis</a>
          <a href="#cotizador-misc" class="nav-tab voyapp-tab hide"                      module="cotizador" targets="cotizador-misc">Misceláneo</a>

          <a href="#seguimiento-principal" class="nav-tab voyapp-tab voyapp-module-main-tab hide" module="seguimiento" targets="seguimiento-principal">Principal</a>
          <a href="#seguimiento-misc" class="nav-tab voyapp-tab hide" module="seguimiento" targets="seguimiento-misc">Misceláneo</a>
        </nav>
        <table class="form-table">
          <?php $this->generate_settings_html(); ?>
        </table>
      </div>
      <?php
    }
    
    public function calculate_shipping($package = array()){
      add_filter( 'woocommerce_package_rates', array($this, 'reorderShippingMethods'), 10, 2 );
      try{
        if((!$package['destination']['city'] || !$package['destination']['address']) || $this->settings['api'] == ""){
          $this->write_log("ERROR: NO SE EJECUTARÁ LA CONSULTA PORQUE NO ESTÁ ESTABLECIDA LA APIKEY O NO SE HA SELECCIONADO UN DESTINO + DIRECCIÓN.");
          return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
        }
        //Envíos gratis  
        if($this->freeShipping($package) && ($this->free_shipping_display_mode == 1 ||  $this->free_shipping_display_mode == 3)  ){
          if($this->free_shipping_display_mode == 3){
            add_filter( 'woocommerce_package_rates', array($this, 'freeShippingOnly') , 11, 2 );
          }
          return;
        }
        //Ciudades ignoradas
        if($this->ignoreCity($package)){
          return;
        }
        
        $headers = $this->getRequestHeaders();
        $data = $this->getRequestInitialData($package);
        if(!$data){
          $this->write_log("ERROR: NO SE EJECUTARÁ LA CONSULTA PORQUE LA CIUDAD SOLICITADA NO ESTÁ MAPEADA.");
          $this->checkAndAddDefaultShipping();
          return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
        }
        $apiUrl = "";
        if($this->test_mode == 'yes'){
          $apiUrl = VOYA_ENDPOINTS['get_rates_test_mode'];
        }else{
          $apiUrl = VOYA_ENDPOINTS['get_rates'];
        }
        $weight = 0;
        $finalPackage = array(0,0,0);
        $packagePerProduct = array();
        $packageQty = count($package["contents"]);
        $counter = 0;
        foreach ($package['contents'] as $item_id => $values){
          $_product = $values['data'];
          $productDimensions = $this->validateAndGetDimensions($_product);
          $weight += $productDimensions["weight"] * $values['quantity'];
          $packagePerProduct[$counter] = array(0,$productDimensions["length"],$productDimensions["width"],$productDimensions["height"]);
          sort($packagePerProduct[$counter]);
          $packagePerProduct[$counter][1] = $packagePerProduct[$counter][1] * $values['quantity'];
          sort($packagePerProduct[$counter]);
          $packagePerProduct[$counter][0] = $packagePerProduct[$counter][1] * $packagePerProduct[$counter][2] * $packagePerProduct[$counter][3];
          $counter++;
        }
        usort($packagePerProduct, function($a, $b) {
          return $b[0] - $a[0];
        });
        
        for ($counter=0; $counter < $packageQty; $counter++) {
          $finalPackage[0] = $finalPackage[0] + $packagePerProduct[$counter][1];
          if ($packagePerProduct[$counter][2] > $finalPackage[1]) {
            $finalPackage[1] = $packagePerProduct[$counter][2];
          }
          if ($packagePerProduct[$counter][3] > $finalPackage[2]) {
            $finalPackage[2] = $packagePerProduct[$counter][3];
          }
          sort($finalPackage);
        }
        $totals = WC()->cart->get_totals();
        $subtotalConDescuentos = round($totals["cart_contents_total"] + $totals["cart_contents_tax"] + $totals["fee_total"] + $totals["fee_tax"],0,PHP_ROUND_HALF_UP);

        $data["height"] = ceil(wc_get_dimension($finalPackage[0],'cm',$this->dunit));
        $data["width"] = ceil(wc_get_dimension($finalPackage[1],'cm',$this->dunit));
        $data["length"] = ceil(wc_get_dimension($finalPackage[2],'cm',$this->dunit));
        $data["volume"] = $data["height"] * $data["width"] *  $data["length"];
        $data["weight"] = wc_get_weight($weight, 'kg', $this->wunit);
        $data["total"] = $subtotalConDescuentos;
        $result = wp_remote_post($apiUrl, array(
          'headers' => $headers,
          'method' => 'POST',
          'sslverify' => $this->getSSLVerify(),
          'body' => http_build_query($data),
          'timeout' => VOYA_CALL_TIMEOUT
          )
        );
        
        if (is_wp_error($result)) {
          $this->write_log("ERROR IS_WP_ERROR");
          $this->write_log($result);
          $this->checkAndAddDefaultShipping();
          return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
        }
        
        $decoded = json_decode($result['body']);
        if (isset($decoded->status) && $decoded->status == 'ERROR') {
          $this->write_log("ERROR ".VOYA_APP_NAME." - MENSAJE INFORMATIVO:");
          $this->write_log($decoded->message);
          $this->checkAndAddDefaultShipping();
          return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
        }
        if(isset($decoded->message) && $decoded->message != "OK"){
          switch ($decoded->message) {
            case 'Unauthenticated.':
              $this->write_log("ERROR: PROBLEMA DE AUTENTICACIÓN EN EL SISTEMA VOYA.");
              break;
            case 'Too Many Attempts.':
              $this->write_log("ERROR: DEMASIADAS CONSULTAS CONCURRENTES.");
              break;
            default:
              $this->write_log("ERROR: ".$decoded->message);
              break;
          }
          $this->checkAndAddDefaultShipping();
          return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
        }
        if (isset($decoded->couriers) && count($decoded->couriers)>0) {
          foreach ($decoded->couriers as $courier) {
            $priceVariation = ($courier->cost / 100) * (int)$this->default_percentage;
            $cost = (int) ($courier->cost + $priceVariation);
            if(isset($this->round_up) && !empty($this->round_up) && $this->round_up > 0){
              $cost = $this->roundUp($cost,$this->round_up);
            }
            $totalDays = 'N/D';
            $dayFormat = '';
            if($this->transit_days == 'yes' && isset($courier->transit_days) && !empty($courier->transit_days)){
              $totalDays = $courier->transit_days + abs($this->additional_transit_days);
              $dayFormat = $totalDays > 1 ? ' días hábiles' : ' día hábil';
            }

            $displayText = '';
            if(isset($courier->display_text) && !empty($courier->display_text)){
              $displayText = $courier->display_text;
            }
            
            $rate = array(
              'id' => $courier->id,
              'label' => $courier->title,
              'cost' => $cost,
              'meta_data' => array(
                'dias_despacho' => $totalDays.$dayFormat,
                'courier_titulo_alternativo' => $displayText,
                'image' => isset($courier->image) ? $courier->image : 'default.png'
                )
              );
              $this->add_rate($rate);
          }
        }else{
          $this->write_log("INFO: EL SISTEMA ".VOYA_APP_NAME." HA ENTREGADO UNA RESPUESTA VACÍA. ESTO PUEDE DEBERSE A: 1.- DOMINIO INACTIVO. 2.- SIN COURIERS SELECCIONADOS. 3.- LAS APIS EXTERNAS DE LOS COURIERS NO HAN ENTREGADO UNA RESPUESTA.");
          $this->checkAndAddDefaultShipping();
        } 
      }catch(Exception $e){
        $this->write_log($e->getMessage());
        $this->checkAndAddDefaultShipping();
        return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', false, $package);
      }
    }
      
    private function validateAndGetDimensions($_product){
      $values = array();
      if($_product->get_weight() != null && !empty($_product->get_weight()) && $_product->get_weight() > 0 ){
        $values["weight"] = $_product->get_weight();
      }else{
        $values["weight"] = $this->default_weight;
      }
      
      //Valida LARGO isset, empty y >0
      if($_product->get_length() != null && !empty($_product->get_length()) && $_product->get_length() > 0 ){
        $values["length"] = $_product->get_length();
      }else{
        $values["length"] =  $this->default_length;
      }
      
      //Valida ANCHO isset, empty y >0
      if($_product->get_width()!= null  && !empty($_product->get_width()) && $_product->get_width() > 0 ){
        $values["width"] = $_product->get_width();
      }else{
        $values["width"] = $this->default_width;
      }
      
      //Valida ALTO isset, empty y >0
      if($_product->get_height()!= null  && !empty($_product->get_height()) && $_product->get_height() > 0 ){
        $values["height"] = $_product->get_height();
      }else{
        $values["height"] = $this->default_height;
      }
      return $values;
    }

    private function getSSLVerify(){
      if($this->ssl_verify_flag == 'yes'){
        return true;
      }else{
        return false;
      }
    }
    
    private function getRequestHeaders(){
      $headers = array();
      $headers["Authorization"] = "Bearer ".$this->api;
      $headers["Accept"] = "application/json";
      return $headers;
    }
    
    private function getRequestInitialData($package){
      $destination = $this->mapPlaces($package['destination']['city']);
      if($destination){
        $data = array();
        $data["domain"] = get_site_url();
        $data["platform"] = "wp_wc";
        $data["weight_unit"] = 'kg';
        $data["dimension_unit"] = 'cm';
        $data["destination"] = $destination;
        if(isset($package['destination']['postcode']) && !empty($package['destination']['postcode'])){
          $data["destination_zip_code"] = $package['destination']['postcode'];
        }
        return $data;
      }else{
        return false;
      }
      
    }
    
    private function mapPlaces($city){
      if($this->isSupportedCountry){
        $filename = VOYA_PLUGIN_PATH . '/includes/data/cities/'.$this->store_country.'_VOYA.php';
      }else{
        $filename = VOYA_PLUGIN_PATH . '/includes/data/cities/CL_VOYA.php';
      }
      $cityName = $this->getStandardName($city);
      if (file_exists( $filename ) ) {
        include( $filename );
      }else{
        $this->write_log("ERROR: ARCHIVO ".$filename." NO ENCONTRADO.");
        return false;
      }
      if(isset($mapPlaces) &&  array_key_exists($cityName, $mapPlaces) && isset($mapPlaces[$cityName])){
        return $mapPlaces[$cityName];
      }else{
        $this->write_log("ERROR: INDEX DE CIUDAD ".$cityName." NO ENCONTRADO EN ARCHIVO ".$filename.".");
        return false;
      }
      
    }
    
    private function checkAndAddDefaultShipping(){
      if($this->enable_default_shipping == "yes"){
        $rate = array(
          'id' => "VOYA_DEFAULT_SM",
          'label' => "Envío por pagar",
          'cost' => 0,
          'meta_data' => array(
            'courier_titulo_alternativo' => 'Envío por pagar',
            'image' => 'default.png'
            )
        );
        $this->add_rate($rate);
      }
    }
    
    private function freeShipping($package){
      $evaluate = true;
      if(isset($this->free_shipping_date_limit) && !empty($this->free_shipping_date_limit)){
        $tz = wp_timezone();
        
        $fechaNow = new DateTime("now",$tz);
        $fechaLimite = DateTime::createFromFormat('d/m/Y H:i:s', $this->free_shipping_date_limit.' 23:59:59',$tz);
        
        if($fechaNow > $fechaLimite){
          $evaluate = false;
        }        
      }
      if($evaluate){
        $totals = WC()->cart->get_totals();
        $subtotalConDescuentos = round($totals["cart_contents_total"] + $totals["cart_contents_tax"] + $totals["fee_total"] + $totals["fee_tax"],0,PHP_ROUND_HALF_UP);

        if($this->free_shipping_combine_criteria == 'yes'){
          if(!empty($this->free_shipping_threshold) && 
            $this->free_shipping_threshold > 0 && 
            isset($subtotalConDescuentos) && 
            $subtotalConDescuentos > 0 && 
            $this->free_shipping_threshold <= $subtotalConDescuentos &&
            $this->free_shipping_destination &&
            $this->mapPlaces($package['destination']['city']) &&
            in_array($this->mapPlaces($package['destination']['city']),$this->free_shipping_destination)
            ){
            $rate = array(
              'id' => "VOYA_FREE_SM",
              'label' => "Envío gratis",
              'cost' => 0,
              'meta_data' => array(
                'courier_titulo_alternativo' => 'Envío gratis',
                'image' => 'default.png'
                )
            );
            $this->add_rate($rate);
            return true;
          }else{
            return false;
          }
        }else{
          //Envío gratis segun total compra
          if(!empty($this->free_shipping_threshold) && $this->free_shipping_threshold > 0 && isset($subtotalConDescuentos) && $subtotalConDescuentos > 0 && $this->free_shipping_threshold <= $subtotalConDescuentos){
            $rate = array(
              'id' => "VOYA_FREE_SM",
              'label' => "Envío gratis",
              'cost' => 0,
              'meta_data' => array(
                'courier_titulo_alternativo' => 'Envío gratis',
                'image' => 'default.png'
                )
            );
            $this->add_rate($rate);
            return true;
          }
          //Envio gratis por destino
          elseif ($this->free_shipping_destination) {
            $destination = $this->mapPlaces($package['destination']['city']);
            if($destination && in_array($destination,$this->free_shipping_destination)){
              $rate = array(
                'id' => "VOYA_FREE_SM",
                'label' => "Envío gratis",
                'cost' => 0,
                'meta_data' => array(
                  'courier_titulo_alternativo' => 'Envío gratis',
                  'image' => 'default.png'
                  )
              );
              $this->add_rate($rate);
              return true;
            }
            return false;
          }
          else{
            return false;
          }
        }
      }else{
        return false;
      }
    }
    
    private function ignoreCity($package){
      if (isset($this->ignored_cities) && !empty($this->ignored_cities)) {
        $destination = $this->mapPlaces($package['destination']['city']);
        if($destination && in_array($destination,$this->ignored_cities)){
          $this->write_log('NO SE COTIZARÁ EL DESPACHO YA QUE LA COMUNA "'.$package['destination']['city'].'" SE ENCUENTRA EN LA LISTA DE "IGNORADOS".');
          $this->checkAndAddDefaultShipping();
          return true;
        }
      }
      return false;
    }
    
    function roundUp($n,$x=10) {
      return round(($n+$x/2)/$x)*$x;
    }
    
    private function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
        error_log( print_r( $log, true ) );
      } else {
        error_log( $log );
      }
    }
  
    private function getStandardName($name){
      $stringWithoutSpecialChars = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê', 'Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç', "'", '"', ' '),
        array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a','e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u', 'u', 'u', 'u', 'u','n', 'n', 'c', 'c',  '',  '',  ''),
        $name 
      );
      return strtolower($stringWithoutSpecialChars);
    }

    public function freeShippingOnly($rates) {
      if(isset($rates['VOYA_FREE_SM'])){
        return [$rates['VOYA_FREE_SM']];
      }
      return $rates;
    }

    public function reorderShippingMethods($rates){
      $allRates = [];
      if ($this->shipping_mode_reorder == 2) {
        uasort($rates, function($a, $b) {
          return $a->cost - $b->cost;
        });
      } elseif ($this->shipping_mode_reorder == 3) {
        uasort($rates, function($a, $b) {
          return $b->cost - $a->cost;
        });
      } elseif ($this->shipping_mode_reorder == 4 || $this->shipping_mode_reorder == 5){
        $targetShippingKey = '';
        $targetShippingCost = 0;
        foreach ($rates as $rateKey => $rateValue) {
          if($rateKey != 'VOYA_FREE_SM' && substr($rateKey, 0, strlen('ABIT_')) === 'ABIT_'){
            if($this->shipping_mode_reorder == 4){ //Precio más bajo
              if($targetShippingCost == 0 || ($rateValue->cost <= $targetShippingCost && $rateValue->cost > 0)){
                $targetShippingKey = $rateKey;
                $targetShippingCost = $rateValue->cost;
              }
            }elseif ($this->shipping_mode_reorder == 5) { //Precio más caro
              if($targetShippingCost == 0 || ($rateValue->cost >= $targetShippingCost && $rateValue->cost > 0)){
                $targetShippingKey = $rateKey;
                $targetShippingCost = $rateValue->cost;
              }
            }
          }
        }
        if($targetShippingKey != ''){
          foreach($rates as $rateKey => $rateValue){
            if($rateKey != $targetShippingKey && $rateKey != 'VOYA_FREE_SM' && substr($rateKey, 0, strlen('ABIT_')) === 'ABIT_'){
              unset($rates[$rateKey]);
            }
          }
        }
      }
      if(isset($rates['VOYA_FREE_SM'])){
        $allRates['VOYA_FREE_SM'] = $rates['VOYA_FREE_SM'];
      }
      foreach ($rates as $rateKey => $rateValue) {
        if($rateKey != 'VOYA_FREE_SM'){
          $allRates[$rateKey] = $rateValue;
        }
      }
      return $allRates;
    }
  }
}
