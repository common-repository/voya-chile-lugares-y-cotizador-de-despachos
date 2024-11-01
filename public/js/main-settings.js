loaderVisibility(true);
jQuery(document).ready(function() {
    jQuery('#woocommerce_voya_despachos_free_shipping_destination').select2();
    jQuery('#woocommerce_voya_despachos_ignored_cities').select2();
    jQuery('#woocommerce_voya_despachos_tracking_couriers').select2();
    jQuery('#woocommerce_voya_despachos_free_shipping_date_limit').datepicker({
        minDate: new Date(),
        showOn: "button",
        buttonText: "Seleccionar fecha",
        showButtonPanel: true,
        prevText: '<Ant',
        nextText: 'Sig>',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié;', 'Juv', 'Vie', 'Sáb'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        closeText: 'Vaciar campo',
        onClose: function (dateText, inst) {
            if (jQuery(window.event.srcElement).hasClass('ui-datepicker-close')) {
                jQuery(this).val('');
            }
        }
    }).attr("readonly", true).next(".ui-datepicker-trigger").addClass("button");
    
    additionalInfoFields();
    let hashValue = location.hash;
    let mainform = document.querySelector('#mainform');
    let mainContainer = document.querySelector('.init-hide');
    let moduleTabs = document.querySelectorAll('.voyapp-module-tab');
    let tabs = document.querySelectorAll('.voyapp-tab');
    let inputs = document.querySelectorAll('.voyapp-despachos-input');

    if(moduleTabs){
        if(tabs){
            tabs.forEach( (singleTab) => {
                if(singleTab.getAttribute('module') == 'principal'){
                singleTab.classList.remove('hide');
                }
            });
        }
        moduleTabs.forEach( (singleModuleTab) => {
            singleModuleTab.addEventListener('click', (e) => {
                if(formValidation(mainform)){
                    moduleTabs.forEach( (singleModuleTab) => { 
                        singleModuleTab.classList.remove('nav-tab-active');
                    });
                    e.target.classList.add('nav-tab-active');
                    if(tabs){
                        tabs.forEach( (singleTab) => {
                            singleTab.classList.remove('nav-tab-active');
                            if(singleTab.getAttribute('module') != e.target.getAttribute('tabtarget')){
                            singleTab.style.display = 'none';
                            }else{
                            if(singleTab.classList.contains('voyapp-module-main-tab')){
                                singleTab.classList.add('nav-tab-active');
                                if(inputs){
                                inputs.forEach( (singleInput) => {
                                    if(singleInput.classList.contains('voyapp-despachos-'+singleTab.getAttribute('targets'))){
                                    singleInput.closest('tr').style.display = 'table-row';
                                    }else{
                                    singleInput.closest('tr').style.display = 'none';
                                    }
                                });
                                }
                            }
                            singleTab.style.display = 'block';
                            }
                        });
                    }   
                }

            });
        });
    }

    if(tabs){
        tabs.forEach( (singleTab) => {
            singleTab.addEventListener('click', (e) => {
                if(formValidation(mainform)){
                    //CSS manipulation:
                    tabs.forEach( (singleTab) => { 
                        singleTab.classList.remove('nav-tab-active');
                    });
                    e.target.classList.add('nav-tab-active');
                    //Inputs manipulation:
                    inputs.forEach( (singleInput) => {
                        if(!singleInput.classList.contains('voyapp-despachos-'+e.target.getAttribute('targets'))){
                            singleInput.closest('tr').style.display = 'none';
                        }else{
                            singleInput.closest('tr').style.display = 'table-row';
                        }
                    });
                }
            });
        });
    }

    if(inputs){
        inputs.forEach( (singleInput) => {
        if(!singleInput.classList.contains('voyapp-despachos-principal-principal')){
            singleInput.closest('tr').style.display = 'none';
        }
        });
    }

    if(hashValue != ''){
        let hashSplitted = hashValue.replace('#', '').split('-');
        let targetModuleTab = hashSplitted[0];
        let innerTab = '';
        if(hashSplitted[1]){
        innerTab = hashSplitted[1];
        }
        moduleTabs.forEach( (singleModuleTab) => { 
        singleModuleTab.classList.remove('nav-tab-active');
        if(singleModuleTab.getAttribute('tabtarget') == targetModuleTab){
            singleModuleTab.classList.add('nav-tab-active');
        }
        });
        tabs.forEach( (singleTab) => {
        singleTab.classList.remove('nav-tab-active');
        if(!innerTab){
            if(singleTab.getAttribute('module') == targetModuleTab){
            if(singleTab.classList.contains('voyapp-module-main-tab')){
                singleTab.classList.add('nav-tab-active');
                if(inputs){
                inputs.forEach( (singleInput) => {
                    if(singleInput.classList.contains('voyapp-despachos-'+singleTab.getAttribute('targets'))){
                    singleInput.closest('tr').style.display = 'table-row';
                    }else{
                    singleInput.closest('tr').style.display = 'none';
                    }
                });
                }
            }
            singleTab.style.display = 'block';
            }else{
            singleTab.style.display = 'none';
            }
        }else{
            if(singleTab.getAttribute('module') == targetModuleTab){
            if(singleTab.getAttribute('targets') == (targetModuleTab+'-'+innerTab)){
                singleTab.classList.add('nav-tab-active');
                if(inputs){
                inputs.forEach( (singleInput) => {
                    if(singleInput.classList.contains('voyapp-despachos-'+singleTab.getAttribute('targets'))){
                    singleInput.closest('tr').style.display = 'table-row';
                    }else{
                    singleInput.closest('tr').style.display = 'none';
                    }
                });
                }
            }
            singleTab.style.display = 'block';
            }else{
            singleTab.style.display = 'none';
            }
        }
        //Si no hay innerTab, dejar activa la tab main del modulo seleccionado
        //Si hay innerTab, dejar activa esa tab

        //habilitar inputs del tab activo


        /* CODIGO A UTILIZAR:
        if(singleTab.getAttribute('module') != e.target.getAttribute('tabtarget')){
            singleTab.style.display = 'none';
        }else{
            if(singleTab.classList.contains('voyapp-module-main-tab')){
            singleTab.classList.add('nav-tab-active');
            if(inputs){
                inputs.forEach( (singleInput) => {
                if(singleInput.classList.contains('voyapp-despachos-'+singleTab.getAttribute('targets'))){
                    singleInput.closest('tr').style.display = 'table-row';
                }else{
                    singleInput.closest('tr').style.display = 'none';
                }
                });
            }
            }
            singleTab.style.display = 'block';
        }
        */
        });
        
    }

    if(mainContainer){
        mainContainer.classList.remove('init-hide');
    }

    loaderVisibility(false);
});

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

function formValidation(form){
    if(form.reportValidity()){
        return true;
    }else{
        return false;
    }
}

function additionalInfoFields(){
    let formContainer = document.querySelector('table.form-table > tbody');

    let formContainerAdditionalFields = `
    <tr valign="top" style="display: none;">
        <th scope="row" class="titledesc">
            <label for="woocommerce_voya_despachos_tracking_couriers">Guía de integración</label>
        </th>
        <td class="forminp">
            <fieldset>
                <legend class="screen-reader-text"><span>Guía de integración</span></legend>
                <input class="input-text regular-input voyapp-despachos-cotizador-principal voyapp-despachos-input" type="hidden" style="" readonly="readonly">
                <p class="description">Si quieres saber cómo integrar el "Cotizador de despachos" Voyapp a tu tienda, <a href="https://voyapp.cl/tutoriales" target="_blank">haz click aquí.</a></p>
            </fieldset>
        </td>
    </tr>
    <tr valign="top" style="display: none;">
        <th scope="row" class="titledesc">
            <label for="woocommerce_voya_despachos_tracking_couriers">Guía de integración</label>
        </th>
        <td class="forminp">
            <fieldset>
                <legend class="screen-reader-text"><span>Guía de integración</span></legend>
                <input class="input-text regular-input voyapp-despachos-seguimiento-principal voyapp-despachos-input" type="hidden" style="" readonly="readonly">
                <p class="description">Si quieres saber cómo integrar el "Formulario de Seguimiento de Pedidos" Voyapp a tu tienda, <a href="https://voyapp.cl/tutoriales/seguimiento-de-pedidos/" target="_blank">haz click aquí.</a></p>
            </fieldset>
        </td>
    </tr>`;
    formContainer.insertAdjacentHTML( 'beforeend', formContainerAdditionalFields );

}