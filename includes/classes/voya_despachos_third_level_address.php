<?php
if (!class_exists('VoyaDespachosThirdLevelAddress')) {
    class VoyaDespachosThirdLevelAddress {
        
        private $countriesWithThirdLevel = ["MX"];

        function __construct() {
            add_filter('woocommerce_checkout_fields', [$this, 'custom_woocommerce_billing_fields']);
            add_action('woocommerce_after_checkout_validation', [$this, 'action_woocommerce_after_checkout_validation'], 10, 2 );
            add_action( 'woocommerce_admin_order_data_after_billing_address', [$this, 'billing_3rd_level_address_display_admin_order_meta'], 10, 1 );
            add_action( 'woocommerce_admin_order_data_after_shipping_address', [$this, 'shipping_3rd_level_address_display_admin_order_meta'], 10, 1 );
        }        
        
        function custom_woocommerce_billing_fields( $fields ){
            $fieldBillingCityExists = false;
            $fieldShippingCityExists = false;

            if(!empty($fields['billing']) && !empty($fields['billing']['billing_city']) && !empty($fields['billing']['billing_city']['priority'])){
                $fieldBillingCityExists = true;
            }
            if(!empty($fields['shipping']) && !empty($fields['shipping']['shipping_city']) && !empty($fields['shipping']['shipping_city']['priority'])){
                $fieldShippingCityExists = true;
            }

            $fields['billing']['billing_3rd_level_address'] = array(
                'label'       => __('Colonia/Barrio/Pueblo', 'woocommerce'),
                'required'    => false, 
                'type'        => 'text',
                'class'       => array('form-row-wide'),
                'priority'    => $fieldBillingCityExists ? ($fields['billing']['billing_city']['priority'] + 1) : '100'
            );
            $fields['shipping']['shipping_3rd_level_address'] = array(
                'label'       => __('Colonia/Barrio/Pueblo', 'woocommerce'),
                'required'    => false, 
                'type'        => 'text',
                'class'       => array('form-row-wide'),
                'priority'    => $fieldShippingCityExists ? ($fields['shipping']['shipping_city']['priority'] + 1) : '100'
            );
            wp_enqueue_script('voya-checkout-third-level-address-js', VOYA_PLUGIN_URL.'/public/js/checkout/voya-third-level-address.js', array('jquery'), VOYA_VERSION, true);
            wp_localize_script( 'voya-checkout-third-level-address-js', 'voya3rdLvlParams',
                array(
                    'countries'   => $this->countriesWithThirdLevel
                )
            );
            return $fields;
        }
        
        function action_woocommerce_after_checkout_validation( $data, $error ) {
            if ( in_array($data['billing_country'], $this->countriesWithThirdLevel) && empty( $data['billing_3rd_level_address'] ) ) {
                $error->add( 'validation', 'Debes ingresar un Colonia/Barrio/Pueblo.' );
            }
            if ( in_array($data['shipping_country'], $this->countriesWithThirdLevel) && empty( $data['shipping_3rd_level_address'] ) ) {
                $error->add( 'validation', 'Debes ingresar un Colonia/Barrio/Pueblo.' );
            }
        }
                
        function billing_3rd_level_address_display_admin_order_meta( $order ){
            $billing_cif = $order->get_meta('_billing_3rd_level_address');
            if( ! empty( $billing_cif ) ) {
                echo '<p><strong>'.__('Colonia/Barrio/Pueblo').':</strong><br>' . $billing_cif . '</p>';
            }
        }

        function shipping_3rd_level_address_display_admin_order_meta( $order ){
            $shipping_cif = $order->get_meta('_shipping_3rd_level_address');
            if( ! empty( $shipping_cif ) ) {
                echo '<p><strong>'.__('Colonia/Barrio/Pueblo').':</strong><br>' . $shipping_cif . '</p>';
            }
        }
    }
}