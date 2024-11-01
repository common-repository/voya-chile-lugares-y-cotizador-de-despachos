(function($) {
    $(document).ready(function () {

        billing_required_or_optional();
        shipping_required_or_optional();
        $( '#billing_country' ).change( billing_required_or_optional );
        $( '#shipping_country' ).change( shipping_required_or_optional );
        function billing_required_or_optional() {
            //Billing
            let countriesWith3rdLevel = voya3rdLvlParams.countries;
            if ( countriesWith3rdLevel.includes($( '#billing_country' ).val()) ) {                            
                $( '#billing_3rd_level_address' ).prop( 'required', true );
                $( 'label[for="billing_3rd_level_address"] .optional' ).remove();
                $( 'label[for="billing_3rd_level_address"]' ).append( '<abbr class="required" title="required">*</abbr>' );
                $( '#billing_3rd_level_address' ).show();
                $( 'label[for="billing_3rd_level_address"]' ).show();
            } else {
                $( '#billing_3rd_level_address' ).removeProp( 'required' );
                $( 'label[for="billing_3rd_level_address"] .required' ).remove();
                if ( $( 'label[for="billing_3rd_level_address"] .optional' ).length == 0 ) {
                    $( 'label[for="billing_3rd_level_address"]' ).append( '<span class="optional">(opcional)</span>' );
                }
                $( '#billing_3rd_level_address' ).val('');
                $( '#billing_3rd_level_address' ).hide();
                $( 'label[for="billing_3rd_level_address"]' ).hide();
            }
        }
        function shipping_required_or_optional() {
            //Shipping
            let countriesWith3rdLevel = voya3rdLvlParams.countries;
            if ( countriesWith3rdLevel.includes($( '#shipping_country' ).val()) ) {  
                $( '#shipping_3rd_level_address' ).prop( 'required', true );
                $( 'label[for="shipping_3rd_level_address"] .optional' ).remove();
                $( 'label[for="shipping_3rd_level_address"]' ).append( '<abbr class="required" title="required">*</abbr>' );
                $( '#shipping_3rd_level_address' ).show();
                $( 'label[for="shipping_3rd_level_address"]' ).show();
            } else {
                $( '#shipping_3rd_level_address' ).removeProp( 'required' );
                $( 'label[for="shipping_3rd_level_address"] .required' ).remove();
                if ( $( 'label[for="shipping_3rd_level_address"] .optional' ).length == 0 ) {
                    $( 'label[for="shipping_3rd_level_address"]' ).append( '<span class="optional">(opcional)</span>' );
                }
                $( '#shipping_3rd_level_address' ).val('');
                $( '#shipping_3rd_level_address' ).hide();
                $( 'label[for="shipping_3rd_level_address"]' ).hide();
            }
        }
    });
})(jQuery);