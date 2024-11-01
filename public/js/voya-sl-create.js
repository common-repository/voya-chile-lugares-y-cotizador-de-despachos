jQuery(document).ready(function() {
  let slSubmitBtn = document.querySelector("#" + slAjaxParams.prefix + "-sl-submit");
  let slSubmitForm = document.querySelector("#" + slAjaxParams.prefix + "-sl-form");
  let slModalHeader = document.querySelector("#" + slAjaxParams.prefix + "-sl-modal-header");
  let valErrors = document.querySelector("#validation-errors");
  let slDownloadButton = document.querySelector("#" + slAjaxParams.prefix + "-sl-download-button");
  if(slSubmitBtn && slSubmitForm && valErrors){
    jQuery(slSubmitBtn).on('click' , function() {
      valErrors.innerHTML = '';
      valErrors.style.display = 'none';
      loaderVisibility(true);
      let slInputs = slSubmitForm.querySelectorAll("input");
      let slSelects = slSubmitForm.querySelectorAll("select");
      let slTextArea = slSubmitForm.querySelectorAll("textarea");

      
      
      let form = {};
      let errors = '<ul>';
      let isValid = true;
      slInputs.forEach( input => {
        if(input.hasAttribute('suubireq') && input.getAttribute('suubireq') == 'true' && (input.value == null || input.value == "") ){
          errors += '<li>El campo <b>' + input.getAttribute('humanreadable') + '</b> es obligatorio. </li>';
          isValid = false;
        }
        if(input.type == 'email' && !validateEmail(input.value)){
          errors += '<li>El campo <b>' + input.getAttribute('humanreadable') + '</b> no tiene el formato adecuado. </li>';
          isValid = false;
        }
      });
      slSelects.forEach( select => {
        if(select.hasAttribute('suubireq') && select.getAttribute('suubireq') == 'true' && (select.value == null || select.value == "") ){
          errors += '<li>El campo <b>' + select.getAttribute('humanreadable') + '</b> es obligatorio. </li>';
          isValid = false;
        }
      });

      if(!isValid){
        errors += '</ul>';
        valErrors.innerHTML = errors;
        valErrors.style.display = 'block';
        if(slModalHeader){
          slModalHeader.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
        }
        loaderVisibility(false);
        return; 
      }


      slInputs.forEach( input => {
        if(input.hasAttribute('extname')){
          form['__sl__'+input.getAttribute('extname')] = input.value;
        }else if(input.getAttribute('name') == 'wcorder_id'){
          form[input.getAttribute('name')] = input.value;
        }
      });
      slTextArea.forEach( textarea => {
        if(textarea.hasAttribute('extname')){
          form['__sl__'+textarea.getAttribute('extname')] = textarea.value;
        }else if(textarea.getAttribute('name') == 'wcorder_id'){
          form[textarea.getAttribute('name')] = textarea.value;
        }
      });
      slSelects.forEach( select => {
        if(select.hasAttribute('extname')){
          form['__sl__'+select.getAttribute('extname')] = select.value;
        }
      });
      form['action'] = slAjaxParams.action;
      try {
        jQuery.post(slAjaxParams.ajaxurl,
        form
        ,function (ret) {
          let jsonResponse = null;
          let parseOK = true;
          try {
              jsonResponse = JSON.parse(ret);
          } catch(e) {
              parseOK = false;
          }
          if(parseOK){
            if(jsonResponse.hasOwnProperty("status") && jsonResponse.status == 'success'){
              downloadPDF(jsonResponse.response, form['wcorder_id']);
              displayAndChangeDownloadButton(slDownloadButton, jsonResponse.response);
            }else{
              let errorMessage = '';
              if(jsonResponse.hasOwnProperty("message") && jsonResponse.message != ''){
                errorMessage = '<p>'+jsonResponse.message+'</p>';
              }else{
                errorMessage = '<p>Ha ocurrido un error inesperado, por favor actualice la página e intente nuevamente.</p>';
              }
              valErrors.innerHTML = errorMessage;
              valErrors.style.display = 'block';
              if(slModalHeader){
                slModalHeader.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
              }
            }
            loaderVisibility(false);
          }else{
            valErrors.innerHTML = '<p>Ha ocurrido un error inesperado, por favor actualice la página e intente nuevamente.</p>';
            valErrors.style.display = 'block';
            if(slModalHeader){
              slModalHeader.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
            }
            loaderVisibility(false);
          }
        }, 'html');
      } catch (error) {
        loaderVisibility(false);
      }
    });
    
  }

  //--Steps Form
  const progress = document.getElementById("step-form-progress");
  const prev = document.getElementById("step-form-prev");
  const next = document.getElementById("step-form-next");
  const circles = document.querySelectorAll(".step-form-circle");
  const stepFormTabs = document.querySelectorAll(".step-form-tab");

  let currentActive = 1;
  let currentActiveTab = 0;

  next.addEventListener("click", () => {
    currentActive++;
    currentActiveTab++;
    if (currentActive > circles.length) currentActive = circles.length;
    update();
  });

  prev.addEventListener("click", () => {
    currentActive--;
    currentActiveTab--;
    if (currentActive < 1) currentActive = 1;
    update();
  });

  const update = () => {
    circles.forEach((circle, index) => {
      if (index < currentActive) circle.classList.add("step-form-active");
      else circle.classList.remove("step-form-active");
    });
    stepFormTabs.forEach((tab, index) => {
      if (index == currentActiveTab){
        tab.style.display = "block";
      } else {
        tab.style.display = "none";
      }
    });
    const actives = document.querySelectorAll(".step-form-active");
    progress.style.width = ((actives.length - 1) / (circles.length - 1)) * 100 + "%";
    if (currentActive === 1){ 
      prev.style.display = 'none';
      next.style.display = 'inline-flex';
      if(slSubmitBtn){
        slSubmitBtn.style.display = 'none';
      }
    } else if (currentActive === circles.length){
      next.style.display = 'none';
      prev.style.display = 'inline-flex';
      if(slSubmitBtn){
        slSubmitBtn.style.display = 'inline-flex';
      }
    } else {
      prev.style.display = 'inline-flex';
      next.style.display = 'inline-flex';
      if(slSubmitBtn){
        slSubmitBtn.style.display = 'none';
      }
    }

    if(slModalHeader){
      slModalHeader.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
    }
  };
});

function validateEmail(email){
  return email.match(
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  );
}

function downloadPDF(pdf, orderId) {
  const linkSource = `data:application/pdf;base64,${pdf}`;
  const downloadLink = document.createElement("a");
  const fileName = 'order_'+orderId+".pdf";
  downloadLink.href = linkSource;
  downloadLink.download = fileName;
  downloadLink.click();
}

function displayAndChangeDownloadButton(button, pdf){
  const linkSource = `data:application/pdf;base64,${pdf}`;
  button.href = linkSource;
  button.style.display = "inline-block";
}

