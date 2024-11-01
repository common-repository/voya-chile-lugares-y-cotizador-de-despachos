jQuery(document).ready(function() {
  jQuery(document).on( 'click', '#voyacl_dismiss_notification_btn', function() {
    let noticeContainer = jQuery("#voyacl_admin_notice");
    let noticeDismissBtn = jQuery("#voyacl_dismiss_notification_btn");
    
    if(noticeDismissBtn){
      noticeDismissBtn.prop('disabled', true);
      noticeDismissBtn.addClass( "disabled" );
      noticeDismissBtn.html('Ocultando notificación...');
    }
    jQuery.post(ajaxParams.ajaxurl,{
        'action':ajaxParams.action
        },function (ret) {
          let jsonResponse = null;
          let parseOK = true;
          try {
              jsonResponse = JSON.parse(ret);
          } catch(e) {
              parseOK = false;
          }
          if(parseOK && jsonResponse.status == 1){
            noticeContainer.hide();
          }else{
            noticeDismissBtn.prop('disabled', false);
            noticeDismissBtn.removeClass( "disabled" );
            noticeDismissBtn.html('Ocultar notificación');
          }
        }, 'html');
  });
});
