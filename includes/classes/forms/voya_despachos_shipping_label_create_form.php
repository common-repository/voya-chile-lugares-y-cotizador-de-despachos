<?php

if (!class_exists('VoyaDespachosShippingLabelCreateForm')) {
	class VoyaDespachosShippingLabelCreateForm {
    const GET_SLT_ACTION = VOYA_PLUGIN_SLUG.'_slt_get_action';
    const CREATE_SL_ACTION = VOYA_PLUGIN_SLUG.'_sl_create_action';

    public function display_page($WCOrderPostId){
      wp_enqueue_script('voya-sl-create-js', VOYA_PLUGIN_URL.'/public/js/voya-sl-create.js', array('jquery'), VOYA_VERSION,true);
      wp_localize_script( 'voya-sl-create-js', 'slAjaxParams',
          array(
            'ajaxurl'   => admin_url( 'admin-ajax.php'),
            'action'    => self::CREATE_SL_ACTION,
            'prefix'    => VOYA_PLUGIN_SLUG
          )
      );
      $order = wc_get_order($WCOrderPostId);
      $shippingLabelTemplates = get_posts([
        'post_type' => VOYA_PLUGIN_SLUG.'-slt',
        'post_status' => 'publish',
        'numberposts' => -1,
        'order_by' => 'id',
        'order' => 'ASC',
      ]);
      $previousB64 = '';
      $sltPostData = array();
      if ( !$shippingLabelTemplates ) {
        $sltPostData['slt_remitente_nombre'] = '';
        $sltPostData['slt_remitente_rut'] = ''; 
        $sltPostData['slt_remitente_telefono'] = '';
        $sltPostData['slt_remitente_email'] = '';
        $sltPostData['slt_remitente_region'] = '';
        $sltPostData['slt_remitente_comuna'] = '';
        $sltPostData['slt_remitente_direccion'] = '';
      } else {
        $meta = get_post_meta($shippingLabelTemplates[0]->ID);
        $sltPostData['slt_remitente_nombre'] = esc_attr($meta['remitente_nombre'][0]);
        $sltPostData['slt_remitente_rut'] = esc_attr($meta['remitente_rut'][0]);
        $sltPostData['slt_remitente_telefono'] = esc_attr($meta['remitente_telefono'][0]);
        $sltPostData['slt_remitente_email'] = esc_attr($meta['remitente_email'][0]);
        $sltPostData['slt_remitente_region'] = esc_attr($meta['remitente_region'][0]);
        $sltPostData['slt_remitente_comuna'] = esc_attr($meta['remitente_comuna'][0]);
        $sltPostData['slt_remitente_direccion'] = esc_attr($meta['remitente_direccion'][0]);
      }

      $destinatario = $this->get_order_shipping_data($order);
      $options = get_option('woocommerce_voya_despachos_settings',[]);
      $apikeySet = false;
      if( isset($options['api']) && !empty($options['api']) ){
        $apikeySet = true;
      }

      if($apikeySet){
      ?> 
        <a class="button thickbox" style="width:100%;text-align: center;" href="<?php echo '#TB_inline?width=600&height=550&inlineId='.VOYA_PLUGIN_SLUG.'-sl-modal';?>" title="Crear etiqueta de despacho">Crear etiqueta de despacho</a><br/>
        <div id="<?php echo VOYA_PLUGIN_SLUG.'-sl-modal'; ?>" style="display:none;">
          <div id="sl-loader" class="blockUI blockOverlay" style="display:none; z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255); opacity: 0.6; cursor: wait; position: absolute;"></div>
          <div style="width:100%;">
          
            <table style="width:100%; border-spacing: 0px;" id="<?php echo VOYA_PLUGIN_SLUG.'-sl-form'; ?>">
              <tr id="<?php echo VOYA_PLUGIN_SLUG.'-sl-modal-header'; ?>" style="vertical-align: top;">
                <td colspan="2" style="padding-top: 10px; text-align: right; border-bottom: 0; padding-bottom: 10px;">
                  <?php $this->modal_header($order); ?>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <style>
                    .step-form-header {
                      background: #3498db; 
                      padding: 10px 20px 10px 20px; 
                      text-align: center; 
                      border-radius: 8px 8px 0px 0px;
                    }
                    .step-form-header h3{
                      color: #ffffff;
                    }
                    .step-form-table {
                      border: 3px solid #3498db;
                      border-top: none;
                      margin-top: 0;
                    }
                    .step-form-table-input-container {
                      padding: 10px 10px 5px 10px !important;
                    }
                    .step-form-table-input-container.last-child {
                      padding-bottom: 35px !important;
                    }
                    .step-form-container {
                      padding-left: 10%;
                      padding-right: 10%;
                      padding-top: 10px;
                      text-align: center;
                    }

                    .step-form-progress-container {
                      display: flex;
                      justify-content: space-between;
                      position: relative;
                      margin-bottom: 30px;
                      max-width: 100%;
                      width: 100%;
                    }

                    .step-form-progress-container::before {
                      content: "";
                      background-color: #e0e0e0;
                      position: absolute;
                      top: 50%;
                      left: 0;
                      transform: translateY(-50%);
                      height: 4px;
                      width: 100%;
                      z-index: -1;
                    }

                    .step-form-progress {
                      background-color: #3498db;
                      position: absolute;
                      top: 50%;
                      left: 0;
                      transform: translateY(-50%);
                      height: 4px;
                      width: 0%;
                      z-index: -1;
                      transition: 0.4s ease;
                    }

                    .step-form-circle {
                      background-color: #fff;
                      color: #999;
                      border-radius: 50%;
                      height: 30px;
                      width: 30px;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      border: 3px solid #e0e0e0;
                      transition: 0.4s ease;
                    }

                    .step-form-circle.step-form-active {
                      border-color: #3498db;
                    }

                    .step-form-btn {
                      background-color: #fff;
                      color: #3498db;
                      border: 2px solid #3498db;
                      border-radius: 6px;
                      cursor: pointer;
                      font-family: inherit;
                      padding: 8px 30px;
                      margin: 5px;
                      font-size: 14px;
                    }

                    .step-form-btn:hover{
                      background-color: #3498db;
                      color: #fff;
                      border: 2px solid #3498db;
                      padding: 8px 30px;
                      margin: 5px;
                      font-size: 14px;
                    }

                    .step-form-btn:active {
                      transform: scale(0.98);
                    }

                    .step-form-btn:focus {
                      outline: 0;
                    }

                    .step-form-btn:disabled {
                      background-color: #e0e0e0;
                      cursor: not-allowed;
                    }

                    .step-form-btn-submit {
                      background-color: #3498db;
                      color: #fff;
                      border: 2px solid #3498db;
                      border-radius: 6px;
                      cursor: pointer;
                      font-family: inherit;
                      padding: 8px 30px;
                      margin: 5px;
                      font-size: 14px;
                      transition: all 0.2s ease-in-out;
                    }

                    .step-form-btn-submit:hover {
                      transform: scale(1.023);
                    }
                    
                    .step-form-btn-create-slt {
                      float: right;
                      background-color: transparent;
                      color: #ffffff;
                      border: 2px solid #ffffff;
                      border-radius: 4px;
                      cursor: pointer;
                      padding: 2px 4px;
                      font-size: 12px;
                      transition: all 0.2s ease-in-out;
                      text-decoration: none;
                    }

                    .step-form-btn-create-slt:hover {
                      text-decoration: none;
                      background-color: #ffffff;
                      color: #3498db;
                    }

                    .step-form-btn-create-slt:focus {
                      text-decoration: none;
                      background-color: #ffffff;
                      color: #3498db;
                    }

                    .step-form-btn-create-slt:link {
                      text-decoration: none;
                    }

                    .step-form-btn-create-slt:visited {
                      text-decoration: none;
                    }

                    .step-form-btn-create-slt:active {
                      text-decoration: none;
                    }

                  </style>
                  <script type="text/javascript"> 
                    function printComunaSelectOptions(comunaSelect, region, comunas, mapComunas, comuna = ''){
                      comunaSelect.value = '';
                      comunaSelect.innerHTML = '';
                      if(comunas.hasOwnProperty(region)){
                        let options = '<option value="" disabled selected hidden>Seleccione una opción</option>';
                        comunas[region].forEach(el => {
                          let optionValue = mapComunas[getStandardName(el)];
                          let selected = '';
                          if(optionValue == comuna){
                            selected = 'selected';
                          }
                          options += '<option value="'+optionValue+'" '+selected+'>'+el+'</option>';
                        });
                        comunaSelect.innerHTML = options;
                      }
                    }

                    function loaderVisibility(vis = true){
                      let loader = document.querySelector("#sl-loader");
                      if(loader){
                        if(vis){
                          loader.style.display = 'block';
                        }else{
                          loader.style.display = 'none';
                        }
                      }
                    }

                    function getStandardName(name) {
                      const specialCharacters = ['Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê', 'Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç', "'", '"', ' '];
                      const standardCharacters = ['a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a','e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u', 'u', 'u', 'u', 'u','n', 'n', 'c', 'c',  '',  '',  ''];

                      const stringWithoutSpecialChars = name.replace(new RegExp(specialCharacters.join('|'), 'g'), (match) => standardCharacters[specialCharacters.indexOf(match)]);
                      return stringWithoutSpecialChars.toLowerCase();
                    }
                  </script>
                  <div class="step-form-container">
                  <div class="step-form-progress-container">
                    <div class="step-form-progress" id="step-form-progress"></div>
                    <div class="step-form-circle step-form-active">1</div>
                    <div class="step-form-circle">2</div>
                    <div class="step-form-circle">3</div>
                  </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align: left; border-bottom: 0; padding-bottom: 4px;">
                  <div id="validation-errors" style="display: none; background-color: #f5bbbf; border: 2px solid #d04f56; border-radius: 15px; padding: 0px 10px 0px 10px; margin-top: 15px; margin-bottom: 15px;" ></div>
                </td>
              </tr>
                <tr style="vertical-align: top;">
                  <td class="step-form-tab" style="width:100%;">
                    <div class="step-form-header">
                      <h3>Datos del remitente <?php $this->create_template_link($shippingLabelTemplates); ?></h3>
                    </div>
                    <table class="form-table step-form-table">
                      <tbody>
                        <input type="hidden" name="wcorder_id" value="<?php echo $WCOrderPostId; ?>" suubireq="true" />
                        <?php $this->select_shipping_label_templates($shippingLabelTemplates); ?>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="slt_remitente_nombre"><b>Nombre / Nombre de empresa</b></label>
                                <input style="width: 100%;" type="text" humanreadable="Nombre del remitente" name="slt_remitente_nombre" extname="sender_name" id="slt_remitente_nombre" value="<?php echo $sltPostData['slt_remitente_nombre']; ?>" suubireq="true" />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="slt_remitente_rut"><b>RUT/DNI/VAT</b></label>
                                <input style="width: 100%;" type="text" humanreadable="RUT/DNI/VAT del remitente" name="slt_remitente_rut" extname="sender_vat" id="slt_remitente_rut" value="<?php echo $sltPostData['slt_remitente_rut']; ?>"  suubireq="true" />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="slt_remitente_telefono"><b>Teléfono</b></label>
                                <input style="width: 100%;" type="text" humanreadable="Teléfono del remitente" name="slt_remitente_telefono" extname="sender_phone" id="slt_remitente_telefono" value="<?php echo $sltPostData['slt_remitente_telefono']; ?>" suubireq="true" />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="slt_remitente_email"><b>Email</b></label>
                                <input style="width: 100%;" type="email" humanreadable="Email del remitente" name="slt_remitente_email" extname="sender_email" id="slt_remitente_email" value="<?php echo $sltPostData['slt_remitente_email']; ?>" suubireq="true" />
                            </fieldset>
                          </td>
                        </tr>
                        <?php
                          $this->display_places_inputs($sltPostData['slt_remitente_region'], $sltPostData['slt_remitente_comuna'], 1, $destinatario['estado'], $destinatario['ciudad']);
                        ?>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container last-child">
                            <fieldset>
                                <label for="slt_remitente_direccion"><b>Dirección</b></label>
                                <input style="width: 100%;" type="text" humanreadable="Dirección del remitente" name="slt_remitente_direccion" extname="sender_address" id="slt_remitente_direccion" value="<?php echo $sltPostData['slt_remitente_direccion']; ?>" suubireq="true" />
                            </fieldset>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td class="step-form-tab" style="width:100%; display: none;">
                    <div class="step-form-header">
                      <h3>Datos del destinatario</h3>
                    </div>
                    <table class="form-table step-form-table">
                      <tbody>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="destinatario_empresa"><b>Nombre Empresa (opcional)</b></label>
                                <input style="width: 100%;" type="text" humanreadable="Empresa destinatario" name="destinatario_empresa" extname="receiver_company" id="destinatario_empresa" value="<?php echo $destinatario['empresa']; ?>" />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="destinatario_rut_empresa"><b>RUT/DNI/VAT Empresa (opcional)</b></label>
                                <input style="width: 100%;" type="text" humanreadable="RUT/DNI/VAT Empresa destinatario" name="destinatario_rut_empresa" extname="receiver_company_vat" id="destinatario_rut_empresa" value="" />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="destinatario_nombre"><b>Nombre</b></label>
                                <input style="width: 100%;" type="text" humanreadable="Nombre del destinatario" name="destinatario_nombre" extname="receiver_name" id="destinatario_nombre" value="<?php echo $destinatario['nombre']; ?>" />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="destinatario_apellido"><b>Apellido</b></label>
                                <input style="width: 100%;" type="text" humanreadable="Apellido del destinatario" name="destinatario_apellido" extname="receiver_lastname" id="destinatario_apellido" value="<?php echo $destinatario['apellido']; ?>" />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="destinatario_rut"><b>RUT/DNI/VAT (opcional)</b></label>
                                <input style="width: 100%;" type="text" humanreadable="RUT/DNI/VAT del destinatario" name="destinatario_rut" extname="receiver_vat" id="destinatario_rut" value=""  />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="destinatario_telefono"><b>Teléfono</b></label>
                                <input style="width: 100%;" type="text" humanreadable="Teléfono del destinatario" name="destinatario_telefono" extname="receiver_phone" id="destinatario_telefono" value="<?php echo $destinatario['telefono']; ?>" suubireq="true" />
                            </fieldset>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container">
                            <fieldset>
                                <label for="destinatario_email"><b>Email</b></label>
                                <input style="width: 100%;" type="email" humanreadable="Email del destinatario" name="destinatario_email" extname="receiver_email" id="destinatario_email" value="<?php echo $destinatario['email']; ?>" suubireq="true" />
                            </fieldset>
                          </td>
                        </tr>
                        <?php
                          $this->display_places_inputs($destinatario['estado'], $destinatario['ciudad'], 0);
                        ?>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container last-child">
                            <fieldset>
                                <label for="destinatario_direccion"><b>Dirección</b></label>
                                <input style="width: 100%;" type="text" humanreadable="Dirección del destinatario" name="destinatario_direccion" extname="receiver_address" id="destinatario_direccion" value="<?php echo $destinatario['direccion_1'].$destinatario['direccion_2']; ?>" suubireq="true" />
                            </fieldset>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td class="step-form-tab" style="width:100%; display: none;">
                    <div class="step-form-header">
                      <h3>Información Adicional</h3>
                    </div>
                    
                    <table class="form-table step-form-table">
                      <tbody>
                        <tr valign="top">
                          <td class="forminp step-form-table-input-container last-child">
                            <fieldset>
                                <label for="destinatario_direccion"><b>Nota adicional del pedido (opcional)</b></label>
                                <textarea style="width: 100%; resize: none; height: 120px;" humanreadable="Nota adicional del pedido" name="destinatario_nota" extname="receiver_note" id="destinatario_nota" maxlength="600"><?php echo $destinatario['customer_note']; ?></textarea>
                            </fieldset>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  
                </tr>
                <tr style="vertical-align: top;">
                  <td colspan="2" style="text-align: center; padding-top: 15px;">
                    <button class="step-form-btn" id="step-form-prev" style="display: none; float: left;">Atrás</button>
                    <button class="step-form-btn" id="step-form-next" style="float: right;">Siguiente</button>
                    <button id="<?php echo VOYA_PLUGIN_SLUG.'-sl-submit'; ?>" type="button" class="step-form-btn-submit" style="display: none; float: right;">Crear etiqueta de despacho</button>
                  </td>
                  
                </tr>
              
            </table>
          </div>
        </div>
      <?php
      } else { ?>
        <a class="button" style="width:100%;text-align: center;" href="#" onclick="alert('No se ha ingresado una API KEY de <?php echo VOYA_APP_NAME; ?>. Debe ingresar una para poder usar esta función.')">Crear etiqueta de despacho</a><br/>
      <?php
      }
    }

    private function display_places_inputs($region,$comuna, $remitente = 1, $dRegion = '', $dComuna = ''){
      $isSupportedCountry = false;
      if($country = wc_get_base_location()['country']){
        $isSupportedCountry = in_array($country, VOYA_SUPPORTED_COUNTRIES);
      }
      if(!$isSupportedCountry){
        $this->text_type_places($region,$comuna,$remitente);
      }else{
        $statesFile = VOYA_PLUGIN_PATH.'includes/data/states/'.$country.'.php';
        $citiesFile = VOYA_PLUGIN_PATH.'includes/data/cities/'.$country.'.php';
        $voyaCitiesFile = VOYA_PLUGIN_PATH.'includes/data/cities/'.$country.'_VOYA.php';
        if( file_exists( $statesFile ) && file_exists( $citiesFile ) && file_exists( $voyaCitiesFile ) ){
          include $statesFile;
          include $citiesFile;
          include $voyaCitiesFile;
          if($remitente){
            $pos = strpos($dComuna, '&#039;');
            if ($pos !== false) {
              $dComuna = substr_replace($dComuna, '\'', $pos, strlen('&#039;'));
            }
            $cityStandardName = $this->getStandardName($dComuna);
            if(isset($mapPlaces) &&  array_key_exists($cityStandardName, $mapPlaces) && isset($mapPlaces[$cityStandardName])){
              $dComuna = $mapPlaces[$cityStandardName];
            }
            $this->select_type_places($states[$country], $places[$country], $mapPlaces, $region, $comuna, $remitente, $dRegion, $dComuna);
          }else{
            $this->select_type_places($states[$country], $places[$country], $mapPlaces, $region, $comuna, $remitente);
          }
        }else{
          if($remitente){
            $this->text_type_places($region, $comuna, $remitente);
          }else{
            $this->text_type_places($region, $comuna, $remitente);
          }
        }        
      }
    }

    private function text_type_places($region, $comuna, $remitente = 1){
      if($remitente){
        $prefix = 'slt_remitente_';
        $entity = 'remitente';
        $externalEntity = 'sender_';
      }else{
        $prefix = 'destinatario_';
        $entity = 'destinatario';
        $externalEntity = 'receiver_';
      }
      ?>       
        <tr valign="top">
          <td class="forminp step-form-table-input-container">
            <fieldset>
              <label for="<?php echo $prefix; ?>region"><b>Estado/Región</b></label>
              <input style="width: 100%;" type="text" humanreadable="Estado/Región del <?php echo $entity; ?>" extname="<?php echo $externalEntity; ?>state" name="<?php echo $prefix; ?>region" id="<?php echo $prefix; ?>region" value="<?php echo $region; ?>" suubireq="true" />
            </fieldset>
          </td>
        </tr>
        <tr valign="top">
          <td class="forminp step-form-table-input-container">
            <fieldset>
                <label for="<?php echo $prefix; ?>comuna"><b>Ciudad/Comuna/Municipio</b></label>
                <input style="width: 100%;" type="text" humanreadable="Ciudad/Comuna/Municipio del <?php echo $entity; ?>" extname="<?php echo $externalEntity; ?>city" name="<?php echo $prefix; ?>comuna" id="<?php echo $prefix; ?>comuna" value="<?php echo $comuna; ?>" suubireq="true" />
            </fieldset>
          </td>
        </tr>
      <?php
    }

    private function select_type_places($states, $places, $mapPlaces, $region, $comuna, $remitente = 1, $dRegion = '', $dComuna = ''){
        
        if($remitente){
          $this->js_nested_selects($states, $places, $mapPlaces, $region, $comuna, $dRegion, $dComuna);
          $prefix = 'slt_remitente_';
          $entity = 'remitente';
          $externalEntity = 'sender_';
        }else{
          $prefix = 'destinatario_';
          $entity = 'destinatario';
          $externalEntity = 'receiver_';
        }
      ?>
        <tr valign="top">
        <td class="forminp step-form-table-input-container">
            <fieldset>
              <label for="<?php echo $prefix; ?>region"><b>Estado/Región</b></label>
              <select style="width: 100%; max-width: 100%;" humanreadable="Estado/Región del <?php echo $entity; ?>" extname="<?php echo $externalEntity; ?>state" name="<?php echo $prefix; ?>region" id="<?php echo $prefix; ?>region" suubireq="true">
                <?php
                  foreach ($states as $singleStateKey => $singleStateValue) {
                    echo '<option value="'.$singleStateKey.'">'.$singleStateValue.'</option>';
                  }
                ?>
              </select>
            </fieldset>
          </td>
        </tr>
        <tr valign="top">
          <td class="forminp step-form-table-input-container">
            <fieldset>
              <label for="<?php echo $prefix; ?>comuna"><b>Ciudad/Comuna/Municipio</b></label>
              <select style="width: 100%; max-width: 100%;" humanreadable="Ciudad/Comuna/Municipio del <?php echo $entity; ?>" extname="<?php echo $externalEntity; ?>city" name="<?php echo $prefix; ?>comuna" id="<?php echo $prefix; ?>comuna" suubireq="true">
              </select>
            </fieldset>
          </td>
        </tr>
      <?php
    }

    private function js_nested_selects($states, $places, $mapPlaces, $region, $comuna, $dRegion, $dComuna){
      ?>
        <script type="text/javascript">
          document.addEventListener('DOMContentLoaded', function(){
            let regionSelect = document.querySelector("#<?php echo VOYA_PLUGIN_SLUG; ?>-sl-modal select[name='slt_remitente_region']");
            let comunaSelect = document.querySelector("#<?php echo VOYA_PLUGIN_SLUG; ?>-sl-modal select[name='slt_remitente_comuna']");
            let destinatarioRegionSelect = document.querySelector("#<?php echo VOYA_PLUGIN_SLUG; ?>-sl-modal select[name='destinatario_region']");
            let destinatarioComunaSelect = document.querySelector("#<?php echo VOYA_PLUGIN_SLUG; ?>-sl-modal select[name='destinatario_comuna']");

            if(regionSelect && comunaSelect && destinatarioRegionSelect && destinatarioComunaSelect){
              let region = '<?php echo $region; ?>';
              let comuna = '<?php echo $comuna; ?>';
              let dRegion = '<?php echo $dRegion; ?>';
              let dComuna = '<?php echo $dComuna; ?>';
              dComuna = dComuna.replace('&#039;', "'");
              let regiones = <?php echo json_encode($states,true); ?>;
              let comunas = <?php echo json_encode($places,true); ?>;
              let mapComunas = <?php echo json_encode($mapPlaces,true); ?>;

              if(region && comuna){
                regionSelect.value = region;
                printComunaSelectOptions(comunaSelect, region, comunas, mapComunas, comuna);
              }

              if(dRegion && dComuna){
                destinatarioRegionSelect.value = dRegion;
                printComunaSelectOptions(destinatarioComunaSelect, dRegion, comunas, mapComunas, dComuna);
              }

              regionSelect.addEventListener('change', function(){
                printComunaSelectOptions(comunaSelect, regionSelect.value, comunas, mapComunas);
              });

              destinatarioRegionSelect.addEventListener('change', function(){
                printComunaSelectOptions(destinatarioComunaSelect, destinatarioRegionSelect.value, comunas, mapComunas);
              });


            }
          });
        </script>
      <?php
    }

    private function create_template_link($templates){
      if( count($templates) <= 0 ){
      ?>
      <a class="step-form-btn-create-slt" href="<?php echo admin_url('admin.php?page='.VOYA_PLUGIN_SLUG.'-shipping-label-template-add'); ?>">Crear plantilla</a>
      <?php
      }
    }

    private function modal_header($order){
      $b64 = '';
      if(VOYA_WC_HPOS){
        $WCOrderMeta = $order->get_meta('_voya_shipping_label', true);
        if( isset($WCOrderMeta) && !empty($WCOrderMeta) ){
          $b64 = esc_attr($WCOrderMeta);
        }
      }else{
        $WCOrderMeta = get_post_meta($order->get_id());
        if( isset($WCOrderMeta['_voya_shipping_label']) && isset($WCOrderMeta['_voya_shipping_label'][0]) && !empty($WCOrderMeta['_voya_shipping_label'][0]) ){
          $b64 = esc_attr($WCOrderMeta['_voya_shipping_label'][0]);
        }
      }

      if($b64){
      ?>
        <a id="<?php echo VOYA_PLUGIN_SLUG; ?>-sl-download-button" class="button" download="order_<?php echo $order->get_id(); ?>.pdf" href="data:application/pdf;base64,<?php echo $b64;?>" style="background-color: #62b862; border-color: #009500; color: white;">Descargar etiqueta existente</a>
      <?php  
      } else {
      ?>
        <a id="<?php echo VOYA_PLUGIN_SLUG; ?>-sl-download-button" class="button" download="order_<?php echo $order->get_id(); ?>.pdf" href="#" style="background-color: #62b862; border-color: #009500; color: white; display:none;">Descargar etiqueta existente</a>
      <?php  
      }
    }

    private function select_shipping_label_templates($templates){
      if(count($templates) > 0){
        wp_enqueue_script('voya-slt-get-js', VOYA_PLUGIN_URL.'/public/js/voya-slt-get.js', array('jquery'), VOYA_VERSION, true);
        wp_localize_script( 'voya-slt-get-js', 'ajaxParams',
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php'),
                'action'    => self::GET_SLT_ACTION
            )
        );
        ?>
          <tr valign="top">
            <td class="forminp step-form-table-input-container">
              <fieldset>
                  <label for="slt-picker"><b>Cargar datos desde plantilla</b></label>
                  <select name="slt-picker" id="slt-picker" style="width: 100%; max-width: 100%;">
                    <?php
                      foreach ($templates as $template) {
                        ?> <option value="<?php echo $template->ID; ?>"><?php echo $template->post_title; ?></option> <?php
                      }
                    ?>
                </select>
              </fieldset>
            </td>
          </tr>
        <?php
      }
    }

    private function get_order_shipping_data($order){
      $shippingData = array();
      if(VOYA_WC_HPOS){
        $metadata = [];
        $shippingAddress =  $order->get_address('shipping');
        $billingAddress =  $order->get_address('billing');

        foreach ($shippingAddress as $shippingKey => $shippingValue) {
          $metadata['_shipping_'.$shippingKey] = $shippingValue;
        }
        foreach ($billingAddress as $billingKey => $billingValue) {
          $metadata['_billing_'.$billingKey] = $billingValue;
        }
        //Nombre
        if( isset($metadata['_shipping_first_name']) && isset($metadata['_shipping_first_name']) && !empty($metadata['_shipping_first_name']) ){
          $shippingData['nombre'] = esc_attr($metadata['_shipping_first_name']);
        }else{
          $shippingData['nombre'] = '';
        }

        //Apellido
        if( isset($metadata['_shipping_last_name']) && isset($metadata['_shipping_last_name']) && !empty($metadata['_shipping_last_name']) ){
          $shippingData['apellido'] = esc_attr($metadata['_shipping_last_name']);
        }else{
          $shippingData['apellido'] = '';
        }

        //Empresa
        if( isset($metadata['_shipping_company']) && isset($metadata['_shipping_company']) && !empty($metadata['_shipping_company']) ){
          $shippingData['empresa'] = esc_attr($metadata['_shipping_company']);
        }else{
          $shippingData['empresa'] = '';
        }

        //Direccion 1
        if( isset($metadata['_shipping_address_1']) && isset($metadata['_shipping_address_1']) && !empty($metadata['_shipping_address_1']) ){
          $shippingData['direccion_1'] = esc_attr($metadata['_shipping_address_1']);
        }else{
          $shippingData['direccion_1'] = '';
        }

        //Direccion 2
        if( isset($metadata['_shipping_address_2']) && isset($metadata['_shipping_address_2']) && !empty($metadata['_shipping_address_2']) ){
          $shippingData['direccion_2'] = ', '.esc_attr($metadata['_shipping_address_2']);
        }else{
          $shippingData['direccion_2'] = '';
        }

        //Ciudad
        if( isset($metadata['_shipping_city']) && isset($metadata['_shipping_city']) && !empty($metadata['_shipping_city']) ){
          $shippingData['ciudad'] = esc_attr($metadata['_shipping_city']);
        }else{
          $shippingData['ciudad'] = '';
        }

        //Estado
        if( isset($metadata['_shipping_state']) && isset($metadata['_shipping_state']) && !empty($metadata['_shipping_state']) ){
          $shippingData['estado'] = esc_attr($metadata['_shipping_state']);
        }else{
          $shippingData['estado'] = '';
        }

        //Telefono
        if( isset($metadata['_shipping_phone']) && isset($metadata['_shipping_phone']) && !empty($metadata['_shipping_phone']) ){
          $shippingData['telefono'] = esc_attr($metadata['_shipping_phone']);
        }elseif( isset($metadata['_billing_phone']) && isset($metadata['_billing_phone']) && !empty($metadata['_billing_phone']) ){
          $shippingData['telefono'] = esc_attr($metadata['_billing_phone']);
        }else{
          $shippingData['telefono'] = '';
        }

        //Email
        if( isset($metadata['_shipping_email']) && isset($metadata['_shipping_email']) && !empty($metadata['_shipping_email']) ){
          $shippingData['email'] = esc_attr($metadata['_shipping_email']);
        }elseif( isset($metadata['_billing_email']) && isset($metadata['_billing_email']) && !empty($metadata['_billing_email']) ){
          $shippingData['email'] = esc_attr($metadata['_billing_email']);
        }else{
          $shippingData['email'] = '';
        }
      }else{
        $metadata = get_post_meta($order->get_id());
        //Nombre
        if( isset($metadata['_shipping_first_name']) && isset($metadata['_shipping_first_name'][0]) && !empty($metadata['_shipping_first_name'][0]) ){
          $shippingData['nombre'] = esc_attr($metadata['_shipping_first_name'][0]);
        }else{
          $shippingData['nombre'] = '';
        }

        //Apellido
        if( isset($metadata['_shipping_last_name']) && isset($metadata['_shipping_last_name'][0]) && !empty($metadata['_shipping_last_name'][0]) ){
          $shippingData['apellido'] = esc_attr($metadata['_shipping_last_name'][0]);
        }else{
          $shippingData['apellido'] = '';
        }

        //Empresa
        if( isset($metadata['_shipping_company']) && isset($metadata['_shipping_company'][0]) && !empty($metadata['_shipping_company'][0]) ){
          $shippingData['empresa'] = esc_attr($metadata['_shipping_company'][0]);
        }else{
          $shippingData['empresa'] = '';
        }

        //Direccion 1
        if( isset($metadata['_shipping_address_1']) && isset($metadata['_shipping_address_1'][0]) && !empty($metadata['_shipping_address_1'][0]) ){
          $shippingData['direccion_1'] = esc_attr($metadata['_shipping_address_1'][0]);
        }else{
          $shippingData['direccion_1'] = '';
        }

        //Direccion 2
        if( isset($metadata['_shipping_address_2']) && isset($metadata['_shipping_address_2'][0]) && !empty($metadata['_shipping_address_2'][0]) ){
          $shippingData['direccion_2'] = ', '.esc_attr($metadata['_shipping_address_2'][0]);
        }else{
          $shippingData['direccion_2'] = '';
        }

        //Ciudad
        if( isset($metadata['_shipping_city']) && isset($metadata['_shipping_city'][0]) && !empty($metadata['_shipping_city'][0]) ){
          $shippingData['ciudad'] = esc_attr($metadata['_shipping_city'][0]);
        }else{
          $shippingData['ciudad'] = '';
        }

        //Estado
        if( isset($metadata['_shipping_state']) && isset($metadata['_shipping_state'][0]) && !empty($metadata['_shipping_state'][0]) ){
          $shippingData['estado'] = esc_attr($metadata['_shipping_state'][0]);
        }else{
          $shippingData['estado'] = '';
        }

        //Telefono
        if( isset($metadata['_shipping_phone']) && isset($metadata['_shipping_phone'][0]) && !empty($metadata['_shipping_phone'][0]) ){
          $shippingData['telefono'] = esc_attr($metadata['_shipping_phone'][0]);
        }elseif( isset($metadata['_billing_phone']) && isset($metadata['_billing_phone'][0]) && !empty($metadata['_billing_phone'][0]) ){
          $shippingData['telefono'] = esc_attr($metadata['_billing_phone'][0]);
        }else{
          $shippingData['telefono'] = '';
        }

        //Email
        if( isset($metadata['_shipping_email']) && isset($metadata['_shipping_email'][0]) && !empty($metadata['_shipping_email'][0]) ){
          $shippingData['email'] = esc_attr($metadata['_shipping_email'][0]);
        }elseif( isset($metadata['_billing_email']) && isset($metadata['_billing_email'][0]) && !empty($metadata['_billing_email'][0]) ){
          $shippingData['email'] = esc_attr($metadata['_billing_email'][0]);
        }else{
          $shippingData['email'] = '';
        }
      }

      //Nota Adicional
      $customerNote = $order->get_customer_note();
      if( isset($customerNote) && !empty($customerNote)){
        $shippingData['customer_note'] = esc_attr($customerNote);
      }else{
        $shippingData['customer_note'] = '';
      }
      return $shippingData;
    }

    private function getStandardName($name){
      $stringWithoutSpecialChars = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê', 'Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç', "'", '"', ' '),
        array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a','e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u', 'u', 'u', 'u', 'u','n', 'n', 'c', 'c',  '',  '',  ''),
        $name 
      );
      return strtolower($stringWithoutSpecialChars);

    }
	}
}
