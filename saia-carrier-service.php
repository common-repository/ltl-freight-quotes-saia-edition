<?php

/**
 * SAIA WooComerce | Get SAIA LTL Quotes Rate Class
 * @package     Woocommerce SAIA Edition
 * @author      <https://eniture.com/>
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SAIA WooComerce | Get SAIA LTL Quotes Rate Class
 */
class SAIA_Get_Shipping_Quotes extends Saia_Liftgate_As_Option
{
    public $en_wd_origin_array;
    public $InstorPickupLocalDelivery;
    public $quote_settings;

    function __construct()
    {
        $this->quote_settings = array();
    }

    /**
     * Create Shipping Package
     * @param $packages
     * @return array
     */
    function saia_shipping_array($packages, $package_plugin = '')
    {
        // FDO
        $EnSaiaFdo = new EnSaiaFdo();
        $en_fdo_meta_data = array();

        $destinationAddressSaia = $this->destinationAddressSaia();
        $residential_detecion_flag = get_option("en_woo_addons_auto_residential_detecion_flag");

        $index = 'ltl-freight-quotes-saia-edition/ltl-freight-quotes-saia-edition.php';
        $plugin_info = get_plugins();
        $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : "";

        $domain = saia_quotes_get_domain();

//      for hazardous feature
        $user_type = get_option('saia_quotes_store_type');
        $plan_no = get_option('saia_quotes_packages_quotes_package');
        $setting = get_option('eniture_plugin_10');
        $feature_type = (isset($setting['saia_quotes_packages_quotes_package']['hazardous_material_feature'])) ? $setting['saia_quotes_packages_quotes_package']['hazardous_material_feature'] : "";

        $this->en_wd_origin_array = (isset($packages['origin'])) ? $packages['origin'] : array();

        $accessorial = array();
        $accessorials = array();

        $lineItem = array();
        $product_name = array();
        $warehouse_appliance_handling_fee = array();

        // check plan for nested material
        $nested_plan = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'nested_material');
        $nestingPercentage = $nestedDimension = $nestedItems = $stakingProperty = [];
        $doNesting = false;

        $product_markup_shipment = 0;
        foreach ($packages['items'] as $item) {
            // Standard Packaging
            $ship_as_own_pallet = isset($item['ship_as_own_pallet']) && $item['ship_as_own_pallet'] == 'yes' ? 1 : 0;
            $vertical_rotation_for_pallet = isset($item['vertical_rotation_for_pallet']) && $item['vertical_rotation_for_pallet'] == 'yes' ? 1 : 0;
            $counter = (isset($item['variantId']) && $item['variantId'] > 0) ? $item['variantId'] : $item['productId'];
            $lineItem[$counter] = array(
                'lineItemHeight' => $item['productHeight'],
                'lineItemLength' => $item['productLength'],
                'lineItemWidth' => $item['productWidth'],
                'lineItemClass' => $item['productClass'],
                'lineItemWeight' => $item['productWeight'],
                'piecesOfLineItem' => $item['productQty'],
                'lineItemPackageCode' => 'PLT',
                // Nesting
                'nestingPercentage' => $item['nestedPercentage'],
                'nestingDimension' => $item['nestedDimension'],
                'nestedLimit' => $item['nestedItems'],
                'nestedStackProperty' => $item['stakingProperty'],

                // Shippable handling units
                'lineItemPalletFlag' => $item['lineItemPalletFlag'],
                'lineItemPackageType' => $item['lineItemPackageType'],
                // Standard Packaging
                'shipPalletAlone' => $ship_as_own_pallet,
                'vertical_rotation' => $vertical_rotation_for_pallet
            );
            $lineItem[$counter] = apply_filters('en_fdo_carrier_service', $lineItem[$counter], $item);
            $product_name[] = $item['product_name'];

            isset($item['nestedMaterial']) && !empty($item['nestedMaterial']) &&
            $item['nestedMaterial'] == 'yes' && !is_array($nested_plan) ? $doNesting = 1 : "";

            if(!empty($item['markup']) && is_numeric($item['markup'])){
                $product_markup_shipment += $item['markup'];
            }

            $lineItem[$counter] = apply_filters('set_warehouse_appliance_handling_fee', $lineItem[$counter], $item);

            if (isset($lineItem[$counter]['en_warehouse_appliance_handling_fee'])) {
                $warehouse_appliance_handling_fee[] = (float)$lineItem[$counter]['en_warehouse_appliance_handling_fee'] * (float)$lineItem[$counter]['piecesOfLineItem'];
            }
        }

        (get_option('saia_liftgate') == 'yes') ? $accessorial[] = 'LiftgateService' : '';
        (get_option('saia_quotes_liftgate_delivery_as_option') == 'yes') ? $accessorial[] = 'LiftgateService' : '';
        (get_option('saia_residential') == 'yes') ? $accessorial[] = 'ResidentialDelivery' : '';

        (get_option('saia_liftgate') == 'yes') ? $accessorials[] = 'L' : '';
        (get_option('saia_quotes_liftgate_delivery_as_option') == 'yes') ? $accessorials[] = 'L' : '';
        (get_option('saia_residential') == 'yes') ? $accessorials[] = 'R' : '';

        //** Start: Shipper and Third party
        $account_number = '';
        $application = '';
        $shipper_account_no = get_option('wc_settings_saia_accountnbr');
        $thirdparty_account_no = get_option('wc_settings_saia_accountnbr_third_party');
        $origin_zip = $packages['origin']['zip'];
        $thirdparty_postal_code = get_option('wc_settings_saia_accountnbr_postal_code');

        // check if SAIA Account number is given on warehouse/dropship against each warehouse/dropship
        $specific_account_enabled = FALSE;
        $origin_specific_account = isset($packages['origin']['saia_account']) ? $packages['origin']['saia_account'] : '';

        if (!empty($origin_specific_account)) {
            $account_number = $origin_specific_account;
            $application = 'Outbound';
        } elseif($origin_zip == $thirdparty_postal_code) {
            $account_number = $shipper_account_no;
            $application = 'Outbound';
        } else {
            $account_number = ($thirdparty_account_no == '') ? $shipper_account_no : $thirdparty_account_no;
            $application = 'ThirdParty';
        }

        // check if SAIA Account number is given on warehouse/dropship against each warehouse/dropship
        if ($thirdparty_postal_code != $origin_zip && !empty($origin_specific_account)) {
            $account_number = $origin_specific_account;
            $specific_account_enabled = TRUE;
        }

        //** End: Shipper and Third party

        // FDO
        $en_fdo_meta_data = $EnSaiaFdo->en_cart_package($packages);

        // Version numbers
        $plugin_versions = $this->en_version_numbers();

        $post_data = array(
            // Version numbers
            'plugin_version' => $plugin_versions["en_current_plugin_version"],
            'wordpress_version' => get_bloginfo('version'),
            'woocommerce_version' => $plugin_versions["woocommerce_plugin_version"],

            'licenseKey' => get_option('wc_settings_saia_plugin_licence_key'),
            'plugin_version' => $plugin_version,
            'serverName' => $this->saia_parse_url($domain),
            'carrierName' => 'saia',
            'carrier_mode' => 'pro',
            'plateform' => 'WordPress',
            'application' => $application,
            'userID' => get_option('wc_settings_saia_userid'),
            'password' => get_option('wc_settings_saia_password'),
            'accountNumber' => $account_number,
            'suspend_residential' => get_option('suspend_automatic_detection_of_residential_addresses'),
            'residential_detecion_flag' => $residential_detecion_flag,
            'originCity' => $packages['origin']['city'],
            'originState' => $packages['origin']['state'],
            'originPostalCode' => $packages['origin']['zip'],
            'originCountry' => $this->saia_get_country_code($packages['origin']['country']),
            'receiverCity' => $destinationAddressSaia['city'],
            'receiverState' => $destinationAddressSaia['state'],
            'receiverZip' => str_replace(' ', '', $destinationAddressSaia['zip']),
            'receiverCountryCode' => $this->saia_get_country_code($destinationAddressSaia['country']),
            'accessorial' => $accessorial,
            'accessorials' => $accessorials,
            // warehouse appliance
            'specific_account_enabled' => $specific_account_enabled,
            'warehouse_appliance_handling_fee' => $warehouse_appliance_handling_fee,
            'sender_origin' => $packages['origin']['location'] . ": " . $packages['origin']['city'] . ", " . $packages['origin']['state'] . " " . $packages['origin']['zip'],
            'product_name' => $product_name,
            'sender_location' => $packages['origin']['location'],
            'commdityDetails' => $lineItem,
            // FDO
            'en_fdo_meta_data' => $en_fdo_meta_data,

            'handlingUnitWeight' => get_option('saia_freight_settings_handling_weight'),
            // Max Handling Unit
            'maxWeightPerHandlingUnit' => get_option('saia_freight_freight_maximum_handling_weight'),
            'doNesting' => $doNesting,
            'origin_markup' => (isset($packages['origin']['origin_markup'])) ? $packages['origin']['origin_markup'] : 0,
            'product_level_markup' => $product_markup_shipment
        );

        $post_data = $this->saia_update_carrier_service($post_data);
        $post_data = apply_filters("en_woo_addons_carrier_service_quotes_request", $post_data, en_woo_plugin_saia_quotes);
        $post_data = apply_filters('en_request_handler', $post_data, 'saia');

//      Hazardous Material
        $hazardous_material = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'hazardous_material');
        if (!is_array($hazardous_material)) {
            if (isset($packages['hazardousMaterial'])) {
                ($packages['hazardousMaterial'] == 'yes') ? $post_data['accessorial'][] = 'Hazardous' : '';
                ($packages['hazardousMaterial'] == 'yes') ? $post_data['hazardous'][] = 'H' : '';
            }
            // FDO
            $post_data['en_fdo_meta_data'] = array_merge($post_data['en_fdo_meta_data'], $EnSaiaFdo->en_package_hazardous($packages, $en_fdo_meta_data));
        }

        // Hold At Terminal
        $hold_at_terminal = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'hold_at_terminal');
        if (!is_array($hold_at_terminal)) {
            (isset($this->quote_settings['HAT_status']) && ($this->quote_settings['HAT_status'] == 'yes')) ? $post_data['holdAtTerminal'] = '1' : '';
        }

//      In-store pickup and local delivery
        $instore_pickup_local_devlivery_action = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
        if (!is_array($instore_pickup_local_devlivery_action)) {
            $post_data = apply_filters('en_wd_saia_standard_plans', $post_data, $post_data['receiverZip'], $this->en_wd_origin_array, $package_plugin);
        }

        // Standard Packaging
        // Configure standard plugin with pallet packaging addon
        $post_data = apply_filters('en_pallet_identify', $post_data);

        do_action("eniture_debug_mood", "Quotes Request (SAIA)", $post_data);
        return $post_data;
    }

    /**
     * Return version numbers
     * @return int
     */
    function en_version_numbers()
    {
        if (!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $plugin_folder = get_plugins('/' . 'woocommerce');
        $plugin_file = 'woocommerce.php';
        $wc_plugin = (isset($plugin_folder[$plugin_file]['Version'])) ? $plugin_folder[$plugin_file]['Version'] : "";
        $get_plugin_data = get_plugin_data(SAIA_MAIN_FILE);
        $plugin_version = (isset($get_plugin_data['Version'])) ? $get_plugin_data['Version'] : '';

        $versions = array(
            "woocommerce_plugin_version" => $wc_plugin,
            "en_current_plugin_version" => $plugin_version
        );

        return $versions;
    }

    /**
     * SAIA Line Items
     * @param $packages
     * @return string
     */
    function saia_get_line_items($packages)
    {
        $lineItem = array();
        foreach ($packages['items'] as $item) {
            $lineItem[] = array(
                'lineItemHeight' => $item['productHeight'],
                'lineItemLength' => $item['productLength'],
                'lineItemWidth' => $item['productWidth'],
                'lineItemClass' => $item['productClass'],
                'lineItemWeight' => $item['productWeight'],
                'piecesOfLineItem' => $item['productQty'],
                'lineItemPackageCode' => 'PLT',
            );
        }
        return $lineItem;
    }

    /**
     * Get SAIA Country Code
     * @param $sCountryName
     * @return string
     */
    function saia_get_country_code($sCountryName)
    {
        switch (trim($sCountryName)) {
            case 'CN':
                $sCountryName = "CAN";
                break;
            case 'CA':
                $sCountryName = "CAN";
                break;
            case 'CAN':
                $sCountryName = "CAN";
                break;
            case 'US':
                $sCountryName = "USA";
                break;
            case 'USA':
                $sCountryName = "USA";
                break;
        }
        return $sCountryName;
    }

    function destinationAddressSaia()
    {
        $en_order_accessories = apply_filters('en_order_accessories', []);
        if (isset($en_order_accessories) && !empty($en_order_accessories)) {
            return $en_order_accessories;
        }

        $saia_woo_obj = new SAIA_Woo_Update_Changes();
        $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $saia_woo_obj->saia_postcode();
        $freight_state = (strlen(WC()->customer->get_shipping_state()) > 0) ? WC()->customer->get_shipping_state() : $saia_woo_obj->saia_getState();
        $freight_country = (strlen(WC()->customer->get_shipping_country()) > 0) ? WC()->customer->get_shipping_country() : $saia_woo_obj->saia_getCountry();
        $freight_city = (strlen(WC()->customer->get_shipping_city()) > 0) ? WC()->customer->get_shipping_city() : $saia_woo_obj->saia_getCity();
        return array(
            'city' => $freight_city,
            'state' => $freight_state,
            'zip' => $freight_zipcode,
            'country' => $freight_country
        );
    }

    /**
     * Get Nearest Address If Multiple Warehouses
     * @param $warehous_list
     * @param $receiverZipCode
     * @return Warehouse Address
     */
    function saia_multi_warehouse($warehous_list, $receiverZipCode)
    {
        if (count($warehous_list) == 1) {
            $warehous_list = reset($warehous_list);
            return $this->saia_origin_array($warehous_list);
        }

        $saia_distance_request = new Get_saia_quotes_distance();
        $accessLevel = "MultiDistance";
        $response_json = $saia_distance_request->saia_quotes_get_distance($warehous_list, $accessLevel, $this->destinationAddressSaia());
        $response_json = json_decode($response_json);


        $origin_with_min_dist = isset($response_json->origin_with_min_dist) && !empty($response_json->origin_with_min_dist) ? $response_json->origin_with_min_dist : array();

        return $this->saia_origin_array($origin_with_min_dist);
    }

    /**
     * Create Origin Array
     * @param $origin
     * @return Warehouse Address Array
     */
    function saia_origin_array($origin)
    {
//      In-store pickup and local delivery
        if (has_filter("en_saia_wd_origin_array_set")) {
            return apply_filters("en_saia_wd_origin_array_set", $origin);
        }

//      Minify Me
        return array('locationId' => $origin->id, 'zip' => $origin->zip, 'city' => $origin->city, 'state' => $origin->state, 'location' => $origin->location, 'country' => $origin->country, 'sender_origin' => $origin->location . ": " . $origin->city . ", " . $origin->state . " " . $origin->zip);
    }

    /**
     * Refine URL
     * @param $domain
     * @return Domain URL
     */
    function saia_parse_url($domain)
    {
        $domain = trim($domain);
        $parsed = parse_url($domain);

        if (empty($parsed['scheme'])) {
            $domain = 'http://' . ltrim($domain, '/');
        }

        $parse = parse_url($domain);
        $refinded_domain_name = $parse['host'];
        $domain_array = explode('.', $refinded_domain_name);

        if (in_array('www', $domain_array)) {
            $key = array_search('www', $domain_array);
            unset($domain_array[$key]);
            if(phpversion() < 8) {
                $refinded_domain_name = implode($domain_array, '.'); 
            }else {
                $refinded_domain_name = implode('.', $domain_array);
            }
        }
        return $refinded_domain_name;
    }

    /**
     * Curl Request To Get Quotes
     * @param $request_data
     * @return json/array
     */
    function saia_get_web_quotes($request_data)
    {

//      Eniture debug mood
        do_action("eniture_debug_mood", "Build Query (SAIA)", http_build_query($request_data));

//          check response from session 
        $currentData = md5(json_encode($request_data));
        $requestFromSession = WC()->session->get('previousRequestData');
        $requestFromSession = ((is_array($requestFromSession)) && (!empty($requestFromSession))) ? $requestFromSession : array();

        if (isset($requestFromSession[$currentData]) && (!empty($requestFromSession[$currentData]))) {

            $this->InstorPickupLocalDelivery = (isset(json_decode($requestFromSession[$currentData])->InstorPickupLocalDelivery) ? json_decode($requestFromSession[$currentData])->InstorPickupLocalDelivery : NULL);
//              Eniture debug mood
            do_action("eniture_debug_mood", "Features (SAIA)", get_option('eniture_plugin_18'));
            do_action("eniture_debug_mood", "Quotes Request (SAIA)", $request_data);
            do_action("eniture_debug_mood", "Build Query (SAIA)", http_build_query($request_data));
            do_action("eniture_debug_mood", "Quotes Response (SAIA)", json_decode($requestFromSession[$currentData]));
            return $this->parse_saia_output($requestFromSession[$currentData], $request_data);
        }

        if (is_array($request_data) && count($request_data) > 0) {
            $saia_curl_obj = new SAIA_Curl_Request();
            $output = $saia_curl_obj->saia_get_curl_response(SAIA_HITTING_DOMAIN_URL . '/index.php', $request_data);
            $response = json_decode($output);

//          set response in session

            if (isset($response->q) && (empty($response->q->error)) && (!empty($response->q->totalNetCharge))) {
                if (isset($response->autoResidentialSubscriptionExpired) && ($response->autoResidentialSubscriptionExpired == 1)) {
                    $flag_api_response = "no";
                    $request_data['residential_detecion_flag'] = $flag_api_response;
                    $currentData = md5(json_encode($request_data));
                }

                $requestFromSession[$currentData] = $output;
                WC()->session->set('previousRequestData', $requestFromSession);
            }

//          Eniture debug mood
            do_action("eniture_debug_mood", "Features (SAIA)", get_option('eniture_plugin_10'));
            do_action("eniture_debug_mood", "Quotes Request (SAIA)", $request_data);
            do_action("eniture_debug_mood", "Quotes Response (SAIA)", json_decode($output));

            $response = json_decode($output);

            $this->InstorPickupLocalDelivery = (isset($response->InstorPickupLocalDelivery) ? $response->InstorPickupLocalDelivery : NULL);

            return $this->parse_saia_output($output, $request_data);
        }
    }

    function parse_saia_output($output, $request_data)
    {
        $result = json_decode($output);

        // FDO
        $en_fdo_meta_data = (isset($request_data['en_fdo_meta_data'])) ? $request_data['en_fdo_meta_data'] : '';

        if (isset($result->fdo_handling_unit)) {
            $en_fdo_meta_data['handling_unit_details'] = $result->fdo_handling_unit;
        }

        $accessorials = [];
        ($this->quote_settings['liftgate_delivery'] == "yes") ? $accessorials[] = "L" : "";
        ($this->quote_settings['residential_delivery'] == "yes") ? $accessorials[] = "R" : "";
        (isset($request_data['hazardous']) && is_array($request_data['hazardous']) && in_array('H', $request_data['hazardous'])) ? $accessorials[] = "H" : "";

        $title = strlen($this->quote_settings['label_as'] > 0) ? $this->quote_settings['label_as'] : 'Freight';

        $label_sufex_arr = $this->filter_label_sufex_array_saia($result);

        // Standard packaging
        $standard_packaging = isset($result->standardPackagingData) ? $result->standardPackagingData : [];

        if (isset($result->q) && empty($result->q->error) && !empty($result->q->totalNetCharge)) {

            $meta_data = [];
            $meta_data['sender_zip'] = (isset($request_data['senderZip'])) ? $request_data['senderZip'] : '';
            $meta_data['sender_location'] = (isset($request_data['sender_location'])) ? $request_data['sender_location'] : '';
            $meta_data['sender_origin'] = (isset($request_data['sender_origin'])) ? $request_data['sender_origin'] : '';
            $meta_data['product_name'] = (isset($request_data['product_name'])) ? $request_data['product_name'] : array();
            $meta_data['accessorials'] = json_encode($accessorials);
            $meta_data['sender_origin'] = $request_data['sender_origin'];
            $meta_data['product_name'] = json_encode($request_data['product_name']);
            $meta_data['address'] = [];
            $meta_data['_address'] = '';

            // Standard Packaging
            $meta_data['standard_packaging'] = wp_json_encode($standard_packaging);

            $quotes = array(
                'id' => 'saia',
                'plugin_name' => 'saia',
                'label' => $title,
                'cost' => $result->q->totalNetCharge,
                'transit_time' => $result->q->transitDays,
                'label_sfx_arr' => $label_sufex_arr,
                'meta_data' => $meta_data,
                'surcharges' => (isset($result->q->surcharges)) ? (array)$result->q->surcharges : 0,
                'origin_markup' => $request_data['origin_markup'],
                'product_level_markup' => $request_data['product_level_markup'],
                'plugin_name' => 'saia',
                'plugin_type' => 'ltl',
                'owned_by' => 'eniture'
            );

            $quotes = array_merge($quotes, $meta_data);

            // warehouse appliance
            $quotes = apply_filters('add_warehouse_appliance_handling_fee', $quotes, $request_data);

            // FDO
            in_array('R', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
            $en_fdo_meta_data['rate'] = $quotes;
            if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                unset($en_fdo_meta_data['rate']['meta_data']);
            }
            $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
            $quotes['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

            // Hold at terminal
            $hold_at_terminal = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'hold_at_terminal');
            if (isset($result->holdAtTerminalResponse, $result->holdAtTerminalResponse->totalNetCharge) && !is_array($hold_at_terminal) && $this->quote_settings['HAT_status'] == 'yes' || (isset($result->holdAtTerminalResponse->severity) && $result->holdAtTerminalResponse->severity != 'ERROR')) {
                $hold_at_terminal_fee = (isset($result->holdAtTerminalResponse->totalNetCharge)) ? $result->holdAtTerminalResponse->totalNetCharge : 0;
                if (isset($this->quote_settings['HAT_fee']) && (strlen($this->quote_settings['HAT_fee']) > 0)) {
                    $SAIA_Freight_Shipping_Class = new SAIA_Freight_Shipping_Class();
                    $hold_at_terminal_fee = $SAIA_Freight_Shipping_Class->add_handling_fee($hold_at_terminal_fee, $this->quote_settings['HAT_fee']);
                }

                $_accessorials = (in_array('H', $accessorials)) ? array('HAT', 'H') : array('HAT');

                $meta_data = [];
                $meta_data['sender_zip'] = (isset($request_data['senderZip'])) ? $request_data['senderZip'] : '';
                $meta_data['sender_location'] = (isset($request_data['sender_location'])) ? $request_data['sender_location'] : '';
                $meta_data['sender_origin'] = (isset($request_data['sender_origin'])) ? $request_data['sender_origin'] : '';
                $meta_data['product_name'] = (isset($request_data['product_name'])) ? $request_data['product_name'] : array();
                $meta_data['accessorials'] = (isset($request_data['accessorials'])) ? array_merge($request_data['accessorials'], $accessorials) : $accessorials;
                $meta_data['accessorials'] = json_encode($meta_data['accessorials']);

                $meta_data['sender_origin'] = $request_data['sender_origin'];
                $meta_data['product_name'] = json_encode($request_data['product_name']);
                // Standard packaging
                $meta_data['standard_packaging'] = wp_json_encode($standard_packaging);

                $_accessorials = (in_array('H', $accessorials)) ? array('HAT', 'H') : array('HAT');
                $meta_data['accessorials'] = json_encode($_accessorials);
                $meta_data['sender_origin'] = $request_data['sender_origin'];
                $meta_data['product_name'] = json_encode($request_data['product_name']);
                $meta_data['address'] = (isset($result->holdAtTerminalResponse->address)) ? json_encode($result->holdAtTerminalResponse->address) : array();

                if(get_option('saia_ltl_hold_at_terminal_remove_address') == 'yes') {
                    $meta_data['_address'] = (isset($result->holdAtTerminalResponse->address, $result->holdAtTerminalResponse->custServicePhoneNbr, $result->holdAtTerminalResponse->distance)) ? $this->get_address_terminal($result->holdAtTerminalResponse->address, $result->holdAtTerminalResponse->custServicePhoneNbr, $result->holdAtTerminalResponse->distance) : '';
                }

                $hold_at_terminal_resp = isset($result->holdAtTerminalResponse) ? $result->holdAtTerminalResponse : [];

                $hat_quotes = array(
                    'id' => 'saiaHat',
                    'plugin_name' => 'saia',
                    'label' => $title,
                    'cost' => $hold_at_terminal_fee,
                    'transit_time' => $result->holdAtTerminalResponse->transitDays,
                    'address' => $meta_data['address'],
                    '_address' => $meta_data['_address'],
                    'label_sfx_arr' => $label_sufex_arr,
                    'hat_append_label' => ' with hold at terminal',
                    '_hat_append_label' => $meta_data['_address'],
                    'meta_data' => $meta_data,
                    'origin_markup' => $request_data['origin_markup'],
                    'product_level_markup' => $request_data['product_level_markup'],
                    'plugin_name' => 'saia',
                    'plugin_type' => 'ltl',
                    'owned_by' => 'eniture'
                );

                $hat_quotes = array_merge($hat_quotes, $meta_data);

                // warehouse appliance
                $hat_quotes = apply_filters('add_warehouse_appliance_handling_fee', $hat_quotes, $request_data);

                // FDO
                $en_fdo_meta_data['rate'] = $hat_quotes;
                if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                    unset($en_fdo_meta_data['rate']['meta_data']);
                }
                $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
                $en_fdo_meta_data['holdatterminal'] = $hold_at_terminal_resp;
                $hat_quotes['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;
                $accessorials_hat = [
                    'holdatterminal' => true,
                    'residential' => false,
                    'liftgate' => false,
                ];
                if (isset($hat_quotes['meta_data']['en_fdo_meta_data']['accessorials'])) {
                    $hat_quotes['meta_data']['en_fdo_meta_data']['accessorials'] = array_merge($hat_quotes['meta_data']['en_fdo_meta_data']['accessorials'], $accessorials_hat);
                } else {
                    $hat_quotes['meta_data']['en_fdo_meta_data']['accessorials']['holdatterminal'] = true;
                }

                if (isset($this->quote_settings['HAT_fee']) &&
                    ($this->quote_settings['HAT_fee'] == "-100%")) {
                    unset($hat_quotes);
                }
            }

            $quotes = apply_filters("en_woo_addons_web_quotes", $quotes, en_woo_plugin_saia_quotes);

            $label_sufex = (isset($quotes['label_sufex'])) ? $quotes['label_sufex'] : array();
            $label_sufex = $this->label_R_wwe_ltl($label_sufex);
            $quotes['label_sufex'] = $label_sufex;

            in_array('R', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
            ($this->quote_settings['liftgate_resid_delivery'] == "yes") && (in_array("R", $label_sufex)) && in_array('L', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true : '';

            // Lift gate delivery as an option
            if (($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (!in_array("R", $label_sufex)) ||
                    ($this->quote_settings['liftgate_resid_delivery'] != "yes"))) {
                $service = $quotes;
                $quotes['id'] .= "WL";

                (isset($quotes['label_sufex']) &&
                    (!empty($quotes['label_sufex']))) ?
                    array_push($quotes['label_sufex'], "L") : // IF
                    $quotes['label_sufex'] = array("L");       // ELSE

                // FDO
                $quotes['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true;
                $quotes['append_label'] = " with lift gate delivery ";

                $liftgate_charge = (isset($service['surcharges']['liftgateFee'])) ? $service['surcharges']['liftgateFee'] : 0;
                $service['cost'] = (isset($service['cost'])) ? $service['cost'] - $liftgate_charge : 0;
                (!empty($service)) && (in_array("R", $service['label_sufex'])) ? $service['label_sufex'] = array("R") : $service['label_sufex'] = array();

                $simple_quotes = $service;

                // FDO
                if (isset($simple_quotes['meta_data']['en_fdo_meta_data']['rate']['cost'])) {
                    $simple_quotes['meta_data']['en_fdo_meta_data']['rate']['cost'] = $service['cost'];
                }
            }

        } else {
            return [];
            $meta_data = [];
            $meta_data['sender_zip'] = (isset($request_data['senderZip'])) ? $request_data['senderZip'] : '';
            $meta_data['sender_location'] = (isset($request_data['sender_location'])) ? $request_data['sender_location'] : '';
            $meta_data['sender_origin'] = (isset($request_data['sender_origin'])) ? $request_data['sender_origin'] : '';
            $meta_data['product_name'] = (isset($request_data['product_name'])) ? $request_data['product_name'] : array();
            $meta_data['accessorials'] = json_encode($accessorials);
            $meta_data['sender_origin'] = $request_data['sender_origin'];
            $meta_data['product_name'] = json_encode($request_data['product_name']);
            $meta_data['address'] = [];
            $meta_data['_address'] = '';
            // Standard packaging
            $meta_data['standard_packaging'] = wp_json_encode($standard_packaging);

            $quotes = array(
                'id' => 'no_quotes',
                'plugin_name' => 'saia',
                'label' => '',
                'cost' => 0,
                'label_sfx_arr' => $label_sufex_arr,
                'meta_data' => $meta_data,
                'surcharges' => (isset($result->q->surcharges)) ? (array)$result->q->surcharges : 0,
                'plugin_name' => 'saia',
                'plugin_type' => 'ltl',
                'owned_by' => 'eniture'
            );

            $quotes = array_merge($quotes, $meta_data);

            // warehouse appliance
            $quotes = apply_filters('add_warehouse_appliance_handling_fee', $quotes, $request_data);

            // FDO
            in_array('R', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
            $en_fdo_meta_data['rate'] = $quotes;
            if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                unset($en_fdo_meta_data['rate']['meta_data']);
            }
            $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
            $quotes['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

            // Hold at terminal
            $hold_at_terminal = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'hold_at_terminal');
            if (!is_array($hold_at_terminal) && $this->quote_settings['HAT_status'] == 'yes') {

                $meta_data = [];
                $meta_data['sender_zip'] = (isset($request_data['senderZip'])) ? $request_data['senderZip'] : '';
                $meta_data['sender_location'] = (isset($request_data['sender_location'])) ? $request_data['sender_location'] : '';
                $meta_data['sender_origin'] = (isset($request_data['sender_origin'])) ? $request_data['sender_origin'] : '';
                $meta_data['product_name'] = (isset($request_data['product_name'])) ? $request_data['product_name'] : array();
                $meta_data['accessorials'] = (isset($request_data['accessorials'])) ? array_merge($request_data['accessorials'], $accessorials) : $accessorials;
                $meta_data['accessorials'] = json_encode($meta_data['accessorials']);

                $meta_data['sender_origin'] = $request_data['sender_origin'];
                $meta_data['product_name'] = json_encode($request_data['product_name']);

                $_accessorials = (in_array('H', $accessorials)) ? array('HAT', 'H') : array('HAT');
                $meta_data['accessorials'] = json_encode($_accessorials);
                $meta_data['sender_origin'] = $request_data['sender_origin'];
                $meta_data['product_name'] = json_encode($request_data['product_name']);
                $meta_data['address'] = [];
                $meta_data['_address'] = '';
                // Standard packaging
                $meta_data['standard_packaging'] = wp_json_encode($standard_packaging);

                $hat_quotes = array(
                    'id' => 'no_quotes_HAT',
                    'plugin_name' => 'saia',
                    'label' => '',
                    'cost' => 0,
                    'label_sufex' => ['HAT'],
                    'meta_data' => $meta_data,
                    'plugin_name' => 'saia',
                    'plugin_type' => 'ltl',
                    'owned_by' => 'eniture'
                );

                $hat_quotes = array_merge($hat_quotes, $meta_data);

                // warehouse appliance
                $hat_quotes = apply_filters('add_warehouse_appliance_handling_fee', $hat_quotes, $request_data);

                // FDO
                $en_fdo_meta_data['rate'] = $hat_quotes;
                if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                    unset($en_fdo_meta_data['rate']['meta_data']);
                }
                $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
                $hat_quotes['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;
                $accessorials_hat = [
                    'holdatterminal' => true,
                    'residential' => false,
                    'liftgate' => false,
                ];
                if (isset($hat_quotes['meta_data']['en_fdo_meta_data']['accessorials'])) {
                    $hat_quotes['meta_data']['en_fdo_meta_data']['accessorials'] = array_merge($hat_quotes['meta_data']['en_fdo_meta_data']['accessorials'], $accessorials_hat);
                } else {
                    $hat_quotes['meta_data']['en_fdo_meta_data']['accessorials']['holdatterminal'] = true;
                }

                if (isset($this->quote_settings['HAT_fee']) &&
                    ($this->quote_settings['HAT_fee'] == "-100%")) {
                    unset($hat_quotes);
                }
            }

            $quotes = apply_filters("en_woo_addons_web_quotes", $quotes, en_woo_plugin_saia_quotes);

            $label_sufex = (isset($quotes['label_sufex'])) ? $quotes['label_sufex'] : array();
            $label_sufex = $this->label_R_wwe_ltl($label_sufex);
            $quotes['label_sufex'] = $label_sufex;

            in_array('R', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
            ($this->quote_settings['liftgate_resid_delivery'] == "yes") && (in_array("R", $label_sufex)) && in_array('L', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true : '';

            // Lift gate delivery as an option
            if (($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (!in_array("R", $label_sufex)) ||
                    ($this->quote_settings['liftgate_resid_delivery'] != "yes"))) {
                $service = $quotes;
                $quotes['id'] .= "WL";

                (isset($quotes['label_sufex']) &&
                    (!empty($quotes['label_sufex']))) ?
                    array_push($quotes['label_sufex'], "L") : // IF
                    $quotes['label_sufex'] = array("L");       // ELSE

                // FDO
                $quotes['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true;
                $quotes['append_label'] = " with lift gate delivery ";

                $liftgate_charge = (isset($service['surcharges']['liftgateFee'])) ? $service['surcharges']['liftgateFee'] : 0;
                $service['cost'] = (isset($service['cost'])) ? $service['cost'] - $liftgate_charge : 0;
                (!empty($service)) && (in_array("R", $service['label_sufex'])) ? $service['label_sufex'] = array("R") : $service['label_sufex'] = array();

                $simple_quotes = $service;

                // FDO
                if (isset($simple_quotes['meta_data']['en_fdo_meta_data']['rate']['cost'])) {
                    $simple_quotes['meta_data']['en_fdo_meta_data']['rate']['cost'] = $service['cost'];
                }
            }
        }

        (!empty($simple_quotes)) ? $quotes['simple_quotes'] = $simple_quotes : "";
        (!empty($hat_quotes)) ? $quotes['hold_at_terminal_quotes'] = $hat_quotes : "";

        return $quotes;
    }

    public function get_address_terminal($address, $phone_nbr, $distance)
    {
        $address_terminal = '';

        $address_terminal .= (isset($distance->Value, $distance->text) || isset($distance->value, $distance->text)) ? ' | ' . $distance->text : '';
        $address_terminal .= (isset($address->Address1)) ? ' | ' . $address->Address1 : '';
        $address_terminal .= (isset($address->City)) ? ' ' . $address->City : '';
        $address_terminal .= (isset($address->State)) ? ' ' . $address->State : '';
        $address_terminal .= (isset($address->Zipcode)) ? ' ' . $address->Zipcode : '';
        $address_terminal .= (strlen($phone_nbr) > 0) ? ' | T: ' . $phone_nbr : '';

        return $address_terminal;
    }

    /**
     * check "R" in array
     * @param array type $label_sufex
     * @return array type
     */
    public function label_R_wwe_ltl($label_sufex)
    {
        if (get_option('saia_residential') == 'yes' && (in_array("R", $label_sufex))) {
            $label_sufex = array_flip($label_sufex);
            unset($label_sufex['R']);
            $label_sufex = array_keys($label_sufex);
        }

        return $label_sufex;
    }

    /**
     * Return SAIA LTL In-store Pickup Array
     */
    function saia_ltl_return_local_delivery_store_pickup()
    {
        return $this->InstorPickupLocalDelivery;
    }

}