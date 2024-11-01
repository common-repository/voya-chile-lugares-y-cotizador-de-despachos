jQuery(document).ready(function($) {
    jQuery(document).on('submit', '#voya-form-tracking', function (e) {
      e.preventDefault();
      let queryBtn = jQuery("#trackShipment");
      queryBtn.trigger("click");
    });
    jQuery(document).on('click', '#trackShipment', function (e) {
        let trackingNo = document.getElementById("voya_tracking_code");
        let courier = document.getElementById("voya_courier");
        let queryBtn = jQuery("#trackShipment");
        queryBtn.val("Cargando...");
        queryBtn.html("Cargando...");
        queryBtn.prop( "disabled", true );
        jQuery("#voya_tracking_response").html('');
        if(!trackingNo.value || !courier.value){
          jQuery("#voya_tracking_response").html(`
          <div class="voya-timeline-container">
            <div class="voya-wrapper">
              <h1 class="voya-heading">Se debe especificar un código de seguimiento y un courier.</h1>
            </div>
          </div>`);
          queryBtn.val("Consultar");
          queryBtn.html("Consultar");
          queryBtn.prop( "disabled", false );
          queryBtn.blur();
          jQuery('html, body').animate({
              scrollTop: $("#voya_tracking_response").offset().top
          }, 1000);
        }else{
          jQuery.post(ajaxParams.ajaxurl, {
            'action':ajaxParams.action,
            'path'  :ajaxParams.path,
            'courier': courier.value,
            'tracking_code': trackingNo.value
          }, function (ret) {
              parseResponse(ret,"#voya_tracking_response");
              queryBtn.val("Consultar");
              queryBtn.html("Consultar");
              queryBtn.prop( "disabled", false );
              queryBtn.blur();
              jQuery('html, body').animate({
                  scrollTop: $("#voya_tracking_response").offset().top
              }, 1000);
          }, 'html');
        }
    });
});

function parseResponse(response,target){
  let jsonResponse = null;
  let parseOK = true;
  let defaultEmptyMsg = "No especificado.";
  if(response){
    try {
        jsonResponse = JSON.parse(response).response;

    } catch(e) {
        parseOK = false;
    }
  }
  if(parseOK){
    if(jsonResponse.hasOwnProperty("status") && jsonResponse.status == "OK"){
      if(jsonResponse.hasOwnProperty("tracking")){
        if(jsonResponse.tracking.hasOwnProperty("actualStatus") && jsonResponse.tracking.actualStatus != ""){
          let generalNote = jsonResponse.tracking.generalNote ? jsonResponse.tracking.generalNote : defaultEmptyMsg;
          let commitmentDate = jsonResponse.tracking.commitmentDate ? jsonResponse.tracking.commitmentDate : defaultEmptyMsg;
          let destination = jsonResponse.tracking.destination ? jsonResponse.tracking.destination : defaultEmptyMsg;
          let timeline = `
              <div class="voya-timeline-container">
                <div class="voya-wrapper">
                  <h1 class="voya-heading">
                    <b>Estado Actual: </b>`+jsonResponse.tracking.actualStatus+`<br>
                    <b>Información adicional: </b>`+generalNote+`<br>
                    <b>Fecha aproximada de entrega: </b>`+commitmentDate+`<br>
                    <b>Destino: </b>`+destination+`<br>
                  </h1>`;
          if(jsonResponse.tracking.history.length > 0){
            timeline += `<ul class="voya-list voya-sessions">`;
            jsonResponse.tracking.history.forEach(elmt => {
              let elDate = elmt.date ? elmt.date : "- Sin Fecha -";
              let elGeneralNote = elmt.generalNote ? elmt.generalNote : "- Sin información adicional -";
              let elStatus = elmt.status ? elmt.status : "- Sin Estado -";
              timeline += `
                <li class="voya-list">
                  <div class="time">`+elStatus+` (`+elDate+`)</div>
                  <p>`+elGeneralNote+`</p>
                </li>
              `;
            });
            timeline += `</ul>`;
          }
           timeline+= `
                </div>
              </div> 
          `;
          jQuery(target).html(timeline);
        }else{
          jQuery(target).html(`
          <div class="voya-timeline-container">
            <div class="voya-wrapper">
              <h1 class="voya-heading">El courier no ha entregado información para el código de seguimiento especificado.</h1>
            </div>
          </div>`);
        }
      }else{
        jQuery(target).html(`
          <div class="voya-timeline-container">
            <div class="voya-wrapper">
              <h1 class="voya-heading">Ha ocurrido un error inesperado.</h1>
            </div>
          </div>`);
      }
    }else{
      jQuery(target).html(`
          <div class="voya-timeline-container">
            <div class="voya-wrapper">
              <h1 class="voya-heading">Ha ocurrido un error inesperado.</h1>
            </div>
          </div>`);
    }
  }else{
    jQuery(target).html(`
          <div class="voya-timeline-container">
            <div class="voya-wrapper">
              <h1 class="voya-heading">`+response+`</h1>
            </div>
          </div>`);
  }
}