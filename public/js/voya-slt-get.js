jQuery(document).ready(function() {

  let sltselector = document.querySelector("#slt-picker");
  if(sltselector){
    let inputNombre = document.querySelector("#slt_remitente_nombre");
    let inputRut = document.querySelector("#slt_remitente_rut");
    let inputTelefono = document.querySelector("#slt_remitente_telefono");
    let inputEmail = document.querySelector("#slt_remitente_email");
    let inputRegion = document.querySelector("#slt_remitente_region");
    let inputComuna = document.querySelector("#slt_remitente_comuna");
    let inputDireccion = document.querySelector("#slt_remitente_direccion");
    jQuery(sltselector).on( 'change', function() {
      loaderVisibility(true);
      jQuery.post(ajaxParams.ajaxurl,{
        'action':ajaxParams.action,
        'sltID': sltselector.value
        },function (ret) {
          let jsonResponse = null;
          let parseOK = true;
          try {
              jsonResponse = JSON.parse(ret);
          } catch(e) {
              parseOK = false;
          }
          if(parseOK){
            if(inputNombre && jsonResponse.hasOwnProperty('remitente_nombre')){
              inputNombre.value = jsonResponse.remitente_nombre;
            }else{
              inputNombre.value = '';
            }

            if(inputRut && jsonResponse.hasOwnProperty('remitente_rut')){
              inputRut.value = jsonResponse.remitente_rut;
            }else{
              inputRut.value = '';
            }

            if(inputTelefono && jsonResponse.hasOwnProperty('remitente_telefono')){
              inputTelefono.value = jsonResponse.remitente_telefono;
            }else{
              inputTelefono.value = '';
            }

            if(inputEmail && jsonResponse.hasOwnProperty('remitente_email')){
              inputEmail.value = jsonResponse.remitente_email;
            }else{
              inputEmail.value = '';
            }

            if(inputRegion && jsonResponse.hasOwnProperty('remitente_region')){
              inputRegion.value = jsonResponse.remitente_region;
              inputRegion.dispatchEvent(new Event('change'));
            }else{
              inputRegion.value = '';
              inputRegion.dispatchEvent(new Event('change'));
            }

            if(inputComuna && jsonResponse.hasOwnProperty('remitente_comuna')){
              setTimeout(function() {
                inputComuna.value = jsonResponse.remitente_comuna;
              }, 1000);
            }else{
              inputComuna.value = '';
            }

            if(inputDireccion && jsonResponse.hasOwnProperty('remitente_direccion')){
              inputDireccion.value = decodeHTMLEntities(jsonResponse.remitente_direccion);
            }else{
              inputDireccion.value = '';
            }
            loaderVisibility(false);
          }else{
            loaderVisibility(false);
            alert('Ha ocurrido un error inesperado, por favor actualice la página e intente nuevamente.');
          }
        }, 'html');
    });
  }
  function decodeHTMLEntities(rawStr) {
    return rawStr.replace(/&#(\d+);/g, ((match, dec) => `${String.fromCharCode(dec)}`));
  }
});
