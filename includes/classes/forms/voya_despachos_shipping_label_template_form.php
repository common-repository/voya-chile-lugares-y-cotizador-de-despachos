<?php

if (!class_exists('VoyaDespachosShippingLabelTemplateForm')) {
	class VoyaDespachosShippingLabelTemplateForm {

		public function display_page($request){
			$params = array();
			$sltPostId = '';
			$sltPostMeta = null;
			if(isset($request['post']) && !empty($request['post']) && is_numeric($request['post']) && is_int((int)$request['post'])){
				$sltPostId = (int) $request['post'];
			}
			$wp_post = get_post( $sltPostId );
			if ( $wp_post && VOYA_PLUGIN_SLUG.'-slt' != get_post_type( $wp_post ) ) {
				wp_redirect(admin_url('admin.php?page='.VOYA_PLUGIN_SLUG.'-shipping-label-template-add'));
			}
			?>
			<div class="wrap" id="<?php echo VOYA_PLUGIN_SLUG; ?>-create-shipping-label-template">
			  <h1 class="wp-heading-inline"><?php
			    if ( !$wp_post ) {
			      echo "Crear nueva plantilla de etiqueta de envío";
			      $sltPostData['post_ID'] = '';
			      $sltPostData['post_title'] = '';
			      $sltPostData['slt_remitente_nombre'] = '';
			      $sltPostData['slt_remitente_rut'] = ''; 
			      $sltPostData['slt_remitente_telefono'] = '';
			      $sltPostData['slt_remitente_email'] = '';
			      $sltPostData['slt_remitente_region'] = '';
			      $sltPostData['slt_remitente_comuna'] = '';
			      $sltPostData['slt_remitente_direccion'] = '';
			    } else {
			      echo "Editar plantilla de etiqueta de envío";
			      $meta = get_post_meta($sltPostId);
			      $sltPostData['post_ID'] = $sltPostId;
			      $sltPostData['post_title'] = esc_attr($wp_post->post_title);
			      $sltPostData['slt_remitente_nombre'] = esc_attr($meta['remitente_nombre'][0]);
			      $sltPostData['slt_remitente_rut'] = esc_attr($meta['remitente_rut'][0]);
			      $sltPostData['slt_remitente_telefono'] = esc_attr($meta['remitente_telefono'][0]);
			      $sltPostData['slt_remitente_email'] = esc_attr($meta['remitente_email'][0]);
			      $sltPostData['slt_remitente_region'] = esc_attr($meta['remitente_region'][0]);
			      $sltPostData['slt_remitente_comuna'] = esc_attr($meta['remitente_comuna'][0]);
			      $sltPostData['slt_remitente_direccion'] = esc_attr($meta['remitente_direccion'][0]);
			    }
			    
			  ?></h1>
			  <p>Crea y edita plantillas de etiquetas de envío para utilizarlas al momento de generar las etiquetas para un pedido.</p>
			  <hr class="wp-header-end">

			  <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="<?php echo VOYA_PLUGIN_SLUG; ?>-shipping-label-template-add">
			    <input type="hidden" id="post_ID" name="post_ID" value="<?php echo $sltPostData['post_ID']; ?>" />
			    <input type="hidden" id="action" name="action" value="<?php echo VOYA_PLUGIN_SLUG.'-shipping-label-template-post' 
			  ?>" />
			    <input type="hidden" id="verb" name="verb" value="save" />
			    <div id="poststuff">
			      <div id="post-body" class="metabox-holder columns-2">
			        <div id="post-body-content">
			          <div id="titlediv">
			            <div id="titlewrap">
			              <input type="text" name="post_title" size="30" value="<?php echo $sltPostData['post_title']; ?>" id="title" placeholder="Ingrese un nombre para la plantilla" required/>
			            </div>
			          </div>
			        </div>
			        <div id="postbox-container-1" class="postbox-container">
		            <div id="submitdiv" class="postbox">
		              <h3>Acción</h3>
		              <div class="inside">
		              <div class="submitbox" id="submitpost">
		              <div id="major-publishing-actions">

		              <?php
		                if ( $sltPostData['post_ID'] ) :
		                  
		              ?>
		              <div id="delete-action">
		                <input type="submit" name="wpcf7-delete" class="delete submitdelete" value="<?php echo esc_attr( __( 'Borrar' ) ); ?>" <?php echo "onclick=\"if (confirm('" . esc_js( __( "¿Está seguro que quiere eliminar esta plantilla?.") ) . "')) {document.querySelector('form input#verb').value = 'delete'; return true;} return false;\""; ?> />
		              </div>
		              <?php endif; ?>

		              <div id="publishing-action">
		                <span class="spinner"></span>
		                <button type="submit" class="button-primary">Guardar</button>
		              </div>
		              <div class="clear"></div>
		              </div>
		              </div>
		              </div>
		            </div>
		          </div>

			        <div id="postbox-container-2" class="postbox-container">
			          <div id="contact-form-editor">
			            <h3>Datos del remitente</h3>
			            <table class="form-table">
			              <tbody>
			                <tr valign="top">
			                  <th scope="row" class="titledesc">
			                    <label for="slt_remitente_nombre">Nombre / Nombre de empresa</label>
			                  </th>
			                  <td class="forminp">
			                    <fieldset>
			                        <input style="width: 50%;" type="text" name="slt_remitente_nombre" id="slt_remitente_nombre" value="<?php echo $sltPostData['slt_remitente_nombre']; ?>" required />
			                      <br>
			                      <p class="description">Ingrese el nombre de la persona o empresa remitente.</p>
			                    </fieldset>
			                  </td>
			                </tr>
			                <tr valign="top">
			                  <th scope="row" class="titledesc">
			                    <label for="slt_remitente_rut">RUT/DNI/VAT</label>
			                  </th>
			                  <td class="forminp">
			                    <fieldset>
			                        <input style="width: 50%;" type="text" name="slt_remitente_rut" id="slt_remitente_rut" value="<?php echo $sltPostData['slt_remitente_rut']; ?>"  required />
			                      <br>
			                      <p class="description">Ingrese el RUT, DNI o VAT de la persona o empresa remitente.</p>
			                    </fieldset>
			                  </td>
			                </tr>
			                <tr valign="top">
			                  <th scope="row" class="titledesc">
			                    <label for="slt_remitente_telefono">Teléfono</label>
			                  </th>
			                  <td class="forminp">
			                    <fieldset>
			                        <input style="width: 50%;" type="text" name="slt_remitente_telefono" id="slt_remitente_telefono" value="<?php echo $sltPostData['slt_remitente_telefono']; ?>" required />
			                      <br>
			                      <p class="description">Ingrese el número de teléfono de la persona o empresa remitente.</p>
			                    </fieldset>
			                  </td>
			                </tr>
			                <tr valign="top">
			                  <th scope="row" class="titledesc">
			                    <label for="slt_remitente_email">Email</label>
			                  </th>
			                  <td class="forminp">
			                    <fieldset>
			                        <input style="width: 50%;" type="text" name="slt_remitente_email" id="slt_remitente_email" value="<?php echo $sltPostData['slt_remitente_email']; ?>" required />
			                      <br>
			                      <p class="description">Ingrese el correo electrónico de la persona o empresa remitente.</p>
			                    </fieldset>
			                  </td>
			                </tr>
                      <?php
                        $this->display_places_inputs($sltPostData['slt_remitente_region'], $sltPostData['slt_remitente_comuna']);
                      ?>
			                <tr valign="top">
			                  <th scope="row" class="titledesc">
			                    <label for="slt_remitente_direccion">Dirección</label>
			                  </th>
			                  <td class="forminp">
			                    <fieldset>
			                        <input style="width: 50%;" type="text" name="slt_remitente_direccion" id="slt_remitente_direccion" value="<?php echo $sltPostData['slt_remitente_direccion']; ?>" required />
			                      <br>
			                      <p class="description">Ingrese la dirección de la persona o empresa remitente.</p>
			                    </fieldset>
			                  </td>
			                </tr>
			              </tbody>
			            </table>
			          </div>
			        </div>
			      </div>
			    </div>
			  </form>
			</div>
			<?php
		}

    public function process_request($request){
      $data = $request;
      $sltPostId = '';
      $extraParams = '';
      if(isset($data['post_ID']) && 
        !empty($data['post_ID']) && 
        is_numeric($data['post_ID']) && 
        is_int((int)$data['post_ID'])
        ){
        $sltPostId = (int) $data['post_ID'];
      }

      $wp_post = get_post( $sltPostId );

      if($data['verb'] == 'save'){
        if ( $wp_post && VOYA_PLUGIN_SLUG.'-slt' == get_post_type( $wp_post ) ) {
          //Editar 
          $shippingLabelTemplate = array(
            'ID' => $wp_post->ID,
            'post_title' => wp_strip_all_tags( $data['post_title'] )
          );
          $sltPostEditedId = wp_update_post($shippingLabelTemplate);
          if(!$sltPostEditedId){
            $extraParams = '&error=1';     
          }else{
            update_post_meta( $sltPostEditedId, 'remitente_nombre', $data['slt_remitente_nombre']);
            update_post_meta( $sltPostEditedId, 'remitente_rut', $data['slt_remitente_rut']);
            update_post_meta( $sltPostEditedId, 'remitente_telefono', $data['slt_remitente_telefono']);
            update_post_meta( $sltPostEditedId, 'remitente_email', $data['slt_remitente_email']);
            update_post_meta( $sltPostEditedId, 'remitente_region', $data['slt_remitente_region']);
            update_post_meta( $sltPostEditedId, 'remitente_comuna', $data['slt_remitente_comuna']);
            update_post_meta( $sltPostEditedId, 'remitente_direccion', $data['slt_remitente_direccion']);
          }
        } else {
          //Crear
          $shippingLabelTemplate = array(
            'post_title'  => wp_strip_all_tags( $data['post_title'] ),
            'meta_input' => array(
              'remitente_nombre' => esc_attr($data['slt_remitente_nombre']),
              'remitente_rut' => esc_attr($data['slt_remitente_rut']),
              'remitente_telefono' => esc_attr($data['slt_remitente_telefono']),
              'remitente_email' => esc_attr($data['slt_remitente_email']),
              'remitente_region' => esc_attr($data['slt_remitente_region']),
              'remitente_comuna' => esc_attr($data['slt_remitente_comuna']),
              'remitente_direccion' => esc_attr($data['slt_remitente_direccion']),
            ),
            'post_type'   => VOYA_PLUGIN_SLUG.'-slt',
            'post_status' => 'publish',
          );
          $sltPostId = wp_insert_post( $shippingLabelTemplate );
          if(!$sltPostId){
            $extraParams = '&error=1';      
          }
        }
      }else{
        //Eliminar
        if ( $wp_post && VOYA_PLUGIN_SLUG.'-slt' == get_post_type( $wp_post ) ) {
          wp_delete_post($sltPostId, true);
        }
      }
      wp_redirect(admin_url('admin.php?page='.VOYA_PLUGIN_SLUG.'-shipping-labels-list'));
    }

    private function display_places_inputs($region,$comuna){
      $isSupportedCountry = false;
      if($country = wc_get_base_location()['country']){
        $isSupportedCountry = in_array($country, VOYA_SUPPORTED_COUNTRIES);
      }
      $cityStandardName = $this->getStandardName($comuna);
      if(!$isSupportedCountry){
        $this->text_type_places($region,$cityStandardName);
      }else{
        $statesFile = VOYA_PLUGIN_PATH.'includes/data/states/'.$country.'.php';
        $citiesFile = VOYA_PLUGIN_PATH.'includes/data/cities/'.$country.'.php';
        $voyaCitiesFile = VOYA_PLUGIN_PATH.'includes/data/cities/'.$country.'_VOYA.php';
        if( file_exists( $statesFile ) && file_exists( $citiesFile ) && file_exists( $voyaCitiesFile ) ){
          include $statesFile;
          include $citiesFile;
          include $voyaCitiesFile;
          $this->select_type_places($states[$country], $places[$country], $mapPlaces, $region, $cityStandardName);
        }else{
          $this->text_type_places($region, $cityStandardName);
        }        
      }
    }

    private function text_type_places($region, $comuna){
      ?> 
        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="slt_remitente_region">Estado/Región</label>
          </th>
          <td class="forminp">
            <fieldset>
              <input style="width: 50%;" type="text" name="slt_remitente_region" id="slt_remitente_region" value="<?php echo $region; ?>" required />
              <br>
              <p class="description">Ingrese la región o estado de la persona o empresa remitente.</p>
            </fieldset>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="slt_remitente_comuna">Ciudad/Comuna/Municipio</label>
          </th>
          <td class="forminp">
            <fieldset>
                <input style="width: 50%;" type="text" name="slt_remitente_comuna" id="slt_remitente_comuna" value="<?php echo $comuna; ?>" required />
              <br>
              <p class="description">Ingrese la ciudad, comuna o municipio de la persona o empresa remitente.</p>
            </fieldset>
          </td>
        </tr>
      <?php
    }

    private function select_type_places($states, $places, $mapPlaces, $region, $comuna){
        $this->js_nested_selects($states, $places, $mapPlaces, $region, $comuna);
      ?>
        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="slt_remitente_region">Estado/Región</label>
          </th>
          <td class="forminp">
            <fieldset>
              <select style="width: 50%;" name="slt_remitente_region" id="slt_remitente_region" required>
                <?php
                  foreach ($states as $singleStateKey => $singleStateValue) {
                    echo '<option value="'.$singleStateKey.'">'.$singleStateValue.'</option>';
                  }
                ?>
              </select>
              <br>
              <p class="description">Ingrese la región o estado de la persona o empresa remitente.</p>
            </fieldset>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="slt_remitente_comuna">Ciudad/Comuna/Municipio</label>
          </th>
          <td class="forminp">
            <fieldset>
              <select style="width: 50%;" name="slt_remitente_comuna" id="slt_remitente_comuna" required>
              </select>
              <br>
              <p class="description">Ingrese la ciudad, comuna o municipio de la persona o empresa remitente.</p>
            </fieldset>
          </td>
        </tr>
      <?php
    }

    private function js_nested_selects($states, $places, $mapPlaces, $region, $comuna){
      ?>
        <script type="text/javascript">
          function printComunaSelectOptions(comunaSelect, region, comunas, mapComunas, comuna = ''){
            comunaSelect.value = '';
            comunaSelect.innerHTML = '';
            if(comunas.hasOwnProperty(region)){
              let options = '';
              comunas[region].forEach(el => {
                let optionValue = mapComunas[getStandardName(el)];
                let selected = '';
                if(optionValue == comuna){
                  selected = 'selected';
                }
                options += '<option value="'+optionValue+'" '+selected+'>'+el+'</option>';
              });
              comunaSelect.innerHTML = options;
            }else{
              console.log("la region "+region+" no tiene comunas");
            }
          }

          function getStandardName(name) {
            const specialCharacters = ['Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê', 'Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç', "'", '"', ' '];
            const standardCharacters = ['a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a','e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u', 'u', 'u', 'u', 'u','n', 'n', 'c', 'c',  '',  '',  ''];

            const stringWithoutSpecialChars = name.replace(new RegExp(specialCharacters.join('|'), 'g'), (match) => standardCharacters[specialCharacters.indexOf(match)]);
            return stringWithoutSpecialChars.toLowerCase();
          }

          document.addEventListener('DOMContentLoaded', function(){

            let regionSelect = document.querySelector("select[name='slt_remitente_region']");
            let comunaSelect = document.querySelector("select[name='slt_remitente_comuna']");

            if(regionSelect && comunaSelect){
              let region = '<?php echo $region; ?>';
              let comuna = '<?php echo $comuna; ?>';

              let regiones = <?php echo json_encode($states,true); ?>;
              let comunas = <?php echo json_encode($places,true); ?>;
              let mapComunas = <?php echo json_encode($mapPlaces,true); ?>;

              if(region && comuna){
                regionSelect.value = region;
                printComunaSelectOptions(comunaSelect, region, comunas, mapComunas, comuna);
              }

              regionSelect.addEventListener('change', function(){
                printComunaSelectOptions(comunaSelect, regionSelect.value, comunas, mapComunas);
              });


            }
          });
        </script>
      <?php
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
