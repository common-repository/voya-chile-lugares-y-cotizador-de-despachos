<?php

class VoyaDespachosDestinos {

    private $states;
    private $places;
    private $store_country;
    private $plugin_url;
    private $plugin_path;
    public static $file;
    /**
    * Construct class
    * @param string $file file path
    * @param boolean $overwriteMKRapel default false, si el plugin MKRapel esta activo, sobreescribe su archivo place-select por uno funcional
    */
    public function __construct($file,$overwriteMKRapel = false) {
        self::$file = $file;
        $this->store_country = wc_get_base_location()['country'];
        if(!$overwriteMKRapel){
            add_action( 'plugins_loaded', array( $this, 'init') );
        }else{
            if(file_exists( WP_PLUGIN_DIR .'/wc-ciudades-y-regiones-de-chile/places/CL.php' )){
                add_action( 'wp_print_scripts', array( $this, 'dequeue_mkrapel_script') );
                add_action( 'wp_enqueue_scripts', array( $this, 'overwrite_mkrapel_script' ) );
            }
        }
    }

    /**
     * WC init
     */
    public function init() {
        $this->init_states();
        $this->init_places();
    }

    /**
     * WC States init
     */
    public function init_states() {
        add_filter('woocommerce_states', array($this, 'wc_states'));
    }

    /**
     * WC Places init
     */
    public function init_places() {
        add_filter( 'woocommerce_billing_fields', array( $this, 'wc_billing_fields' ), 10, 2 );
        add_filter( 'woocommerce_shipping_fields', array( $this, 'wc_shipping_fields' ), 10, 2 );
        add_filter( 'woocommerce_form_field_city', array( $this, 'wc_form_field_city' ), 10, 4 );

        add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
    }

    /**
     * Implement WC States
     * @param mixed $states
     * @return mixed
     */
    public function  wc_states() {
        //get countries allowed by store owner
        $states = array();
        if (! isset( $states[$this->store_country] ) && file_exists($this->get_plugin_path() . '/includes/data/states/' . $this->store_country . '.php')) {
          include($this->get_plugin_path() . '/includes/data/states/' . $this->store_country . '.php');
        }elseif(! isset( $states['CL'] ) && file_exists($this->get_plugin_path() . '/includes/data/states/CL.php')) {
          include($this->get_plugin_path() . '/includes/data/states/CL.php');
        }
        return $states;
    }

    /**
     * Modify billing field
     * @param mixed $fields
     * @param mixed $country
     * @return mixed
     */
    public function wc_billing_fields( $fields, $country ) {
        $fields['billing_city']['type'] = 'city';

        return $fields;
    }

    /**
     * Modify shipping field
     * @param mixed $fields
     * @param mixed $country
     * @return mixed
     */
    public function wc_shipping_fields( $fields, $country ) {
        $fields['shipping_city']['type'] = 'city';

        return $fields;
    }

    /**
     * Implement places/city field
     * @param mixed $field
     * @param string $key
     * @param mixed $args
     * @param string $value
     * @return mixed
     */
    public function wc_form_field_city($field, $key, $args, $value ) {
        // Do we need a clear div?
        if ( ( ! empty( $args['clear'] ) ) ) {
            $after = '<div class="clear"></div>';
        } else {
            $after = '';
        }

        // Required markup
        if ( $args['required'] ) {
            $args['class'][] = 'validate-required';
            $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce'  ) . '">*</abbr>';
        } else {
            $required = '';
        }

        // Custom attribute handling
        $custom_attributes = array();

        if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
            foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        // Validate classes
        if ( ! empty( $args['validate'] ) ) {
            foreach( $args['validate'] as $validate ) {
                $args['class'][] = 'validate-' . $validate;
            }
        }

        // field p and label
        $field  = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
        if ( $args['label'] ) {
            $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required . '</label>';
        }

        // Get Country
        $country_key = $key == 'billing_city' ? 'billing_country' : 'shipping_country';
        $current_cc  = WC()->checkout->get_value( $country_key );

        $state_key = $key == 'billing_city' ? 'billing_state' : 'shipping_state';
        $current_sc  = WC()->checkout->get_value( $state_key );

        // Get country places
        $places = $this->get_places( $current_cc );

        if ( is_array( $places ) ) {

            $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="city_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '">';

            $field .= '<option value="">'. __( 'Select an option&hellip;', 'woocommerce' ) .'</option>';

            if ( $current_sc && array_key_exists( $current_sc, $places ) ) {
                $dropdown_places = $places[ $current_sc ];
            } else if ( is_array($places) && isset($places[0])) {
                $dropdown_places = $places;
                sort( $dropdown_places );
            } else {
                $dropdown_places = $places;
            }

            foreach ( $dropdown_places as $city_name ) {
                if(!is_array($city_name)) {
                    $field .= '<option value="' . esc_attr( $city_name ) . '" '.selected( $value, $city_name, false ) . '>' . $city_name .'</option>';
                }
            }

            $field .= '</select>';

        } else {

            $field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
        }

        // field description and close wrapper
        if ( $args['description'] ) {
            $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
        }

        $field .= '</p>' . $after;

        return $field;
    }
    /**
     * Get places
     * @param string $p_code(default:)
     * @return mixed
     */
    public function get_places( $p_code = null ) {
        if ( empty( $this->places ) ) {
            $this->load_country_places();
        }

        if ( ! is_null( $p_code ) ) {
            return isset( $this->places[ $p_code ] ) ? $this->places[ $p_code ] : false;
        } else {
            return $this->places;
        }
    }
    /**
     * Get country places
     * @return mixed
     */
    public function load_country_places() {
        global $places;        
        if ( ! isset( $places[ $this->store_country ] ) && file_exists( $this->get_plugin_path() . '/includes/data/cities/' . $this->store_country . '.php' ) ) {
          include( $this->get_plugin_path() . '/includes/data/cities/' . $this->store_country . '.php' );
        }elseif ( ! isset( $places['CL'] ) && file_exists( $this->get_plugin_path() . '/includes/data/cities/CL.php' ) ) {
          include( $this->get_plugin_path() . '/includes/data/cities/CL.php' );
        }
        $this->places = $places;
    }

    /**
     * Load scripts
     */
    public function load_scripts() {
        if ( is_cart() || is_checkout() || is_wc_endpoint_url( 'edit-address' ) || is_account_page()) {

            $city_select_path = $this->get_plugin_url() . 'public/js/place-select.js';
            
            wp_enqueue_script( 'wc-city-select', $city_select_path, array( 'jquery', 'woocommerce' ), VOYA_VERSION, true );

            $places = json_encode( $this->get_places() );
            wp_localize_script( 'wc-city-select', 'wc_city_select_params', array(
                'cities' => $places,
                'i18n_select_city_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' )
            ) );
        }
    }   

    /**
     * Get plugin root path
     * @return mixed
     */
    private function get_plugin_path() {
        if (isset($this->plugin_path)) {
            return $this->plugin_path;
        }
        $cleansed = rtrim(plugin_dir_path( self::$file ),'\\');
        $cleansed = rtrim($cleansed,"/");

        $path = $this->plugin_path = $cleansed;

        return untrailingslashit($path);
    }

    /**
     * Get Store allowed countries
     * @return mixed
     */
    private function get_store_allowed_countries() {
        return array_merge( WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries() );
    }

    /**
     * Get plugin url
     * @return mixed
     */
    public function get_plugin_url() {

        if (isset($this->plugin_url)) {
            return $this->plugin_url;
        }

        return $this->plugin_url = plugin_dir_url( self::$file );
    }


    /**
     * Overwrite MKRapel scripts
     */
    public function overwrite_mkrapel_script() {
        if ( is_cart() || is_checkout() || is_wc_endpoint_url( 'edit-address' ) || is_account_page()) {

            $city_select_path = $this->get_plugin_url() . 'public/js/place-select.js';
            
            wp_enqueue_script( 'voya-wc-city-select', $city_select_path, array( 'jquery', 'woocommerce' ), VOYA_VERSION, true );
            $places = json_encode( $this->load_mk_rapel_get_places() );
            wp_localize_script( 'voya-wc-city-select', 'wc_city_select_params', array(
                'cities' => $places,
                'i18n_select_city_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' )
            ) );
        }
    }

    function dequeue_mkrapel_script() {
        wp_dequeue_script( 'wc-city-select' );
    }

    public function load_mkrapel_country_places() {
        global $places;
        if ( ! isset( $places['CL'] ) && file_exists( WP_PLUGIN_DIR .'/wc-ciudades-y-regiones-de-chile/places/CL.php' ) ) {
          include( WP_PLUGIN_DIR .'/wc-ciudades-y-regiones-de-chile/places/CL.php' );
        }
        $this->places = $places;
    }

    public function load_mk_rapel_get_places( $p_code = null ) {
		if ( empty( $this->places ) ) {
            $this->load_mkrapel_country_places();
        }

        if ( ! is_null( $p_code ) ) {
            return isset( $this->places[ $p_code ] ) ? $this->places[ $p_code ] : false;
        } else {
            return $this->places;
        }
	}
}
