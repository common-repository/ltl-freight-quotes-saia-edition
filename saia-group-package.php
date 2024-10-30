<?php

/**
 * SAIA WooComerce | Get Shipping Package Class
 * @package     Woocommerce SAIA Edition
 * @author      <https://eniture.com/>
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SAIA WooComerce | Get Shipping Package Class
 */
class SAIA_Shipping_Get_Package
{

    /**
     * hasLTLShipment
     * @var int
     */
    public $hasLTLShipment = 0;

    /**
     * Errors
     * @var varchar
     */
    public $errors = [];
    public $ValidShipments = 0;
    public $ValidShipmentsArrSaia = [];
    // Images for FDO
    public $en_fdo_image_urls = [];
    // Micro Warehouse
    public $products = [];
    public $dropship_location_array = [];
    public $warehouse_products = [];
    public $destination_Address_saia;
    public $origin = [];

    /**
     * Grouping For Shipments
     * @param $package
     * @param $saia_res_inst
     * @param $freight_zipcode
     * @return Shipment Grouped Array
     * @global $wpdb
     */
    function group_saia_shipment($package, $saia_res_inst, $freight_zipcode)
    {
        $saia_package = [];
        if (empty($freight_zipcode)) {
            return [];
        }
        global $wpdb;
        $weight = 0;
        $dimensions = 0;
        $saia_freight_class = "";
        $saia_enable = false;

        $wc_settings_wwe_ignore_items = get_option("en_ignore_items_through_freight_classification");
        $en_get_current_classes = strlen($wc_settings_wwe_ignore_items) > 0 ? trim(strtolower($wc_settings_wwe_ignore_items)) : '';
        $en_get_current_classes_arr = strlen($en_get_current_classes) > 0 ? array_map('trim', explode(',', $en_get_current_classes)) : [];

        // Micro Warehouse
        $smallPluginExist = 0;
        $saia_package = $items = $items_shipment = [];
        $saia_get_shipping_quotes = new SAIA_Get_Shipping_Quotes();
        $this->destination_Address_saia = $saia_get_shipping_quotes->destinationAddressSaia();

        //threshold
        $weight_threshold = get_option('en_weight_threshold_lfq');
        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;

        $flat_rate_shipping_addon = apply_filters('en_add_flat_rate_shipping_addon', false);

        // Standard Packaging
        $en_ppp_pallet_product = apply_filters('en_ppp_existence', false);

        $counter = 0;
        foreach ($package['contents'] as $item_id => $values) {
            $_product = $values['data'];

            // Images for FDO
            $this->en_fdo_image_urls($values, $_product);

            // Flat rate pricing
            $product_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
            $parent_id = $product_id;
            if(isset($values['variation_id']) && $values['variation_id'] > 0){
                $variation = wc_get_product($values['variation_id']);
                $parent_id = $variation->get_parent_id();
            }
            $en_flat_rate_price = $this->en_get_flat_rate_price($values, $_product);
            if ($flat_rate_shipping_addon && isset($en_flat_rate_price) && strlen($en_flat_rate_price) > 0) {
                continue;
            }

            // Get product shipping class
            $en_ship_class = strtolower($values['data']->get_shipping_class());
            if (in_array($en_ship_class, $en_get_current_classes_arr)) {
                continue;
            }

            // Shippable handling units
            $values = apply_filters('en_shippable_handling_units_request', $values, $values, $_product);
            $shippable = [];
            if (isset($values['shippable']) && !empty($values['shippable'])) {
                $shippable = $values['shippable'];
            }

            $nestedPercentage = 0;
            $nestedDimension = "";
            $nestedItems = "";
            $StakingProperty = "";

            $dimension_unit = get_option('woocommerce_dimension_unit');
            // Convert product dimensions in feet ,centimeter,miles,kilometer into Inches
            if ($dimension_unit == 'ft' || $dimension_unit == 'cm' || $dimension_unit == 'mi' || $dimension_unit == 'km') {

                $dimensions = $this->dimensions_conversion($_product);
                $height = $dimensions['height'];
                $width = $dimensions['width'];
                $length = $dimensions['length'];
            } else {

                $height = wc_get_dimension($_product->get_height(), 'in');
                $width = wc_get_dimension($_product->get_width(), 'in');
                $length = wc_get_dimension($_product->get_length(), 'in');
            }

            $height = ($height == '') ? 0 : $height;
            $width = ($width == '') ? 0 : $width;
            $length = ($length == '') ? 0 : $length;

            $product_weight = round(wc_get_weight($_product->get_weight(), 'lbs'), 2);
            $weight = $product_weight * $values['quantity'];
            $dimensions = (($length * $values['quantity']) * $width * $height);
            $freightClass = $_product->get_shipping_class(); // it define either product marked as ltl or not
            if ($freightClass == 'ltl_freight') {
                $ltl_freight_class = $freightClass;
            }
            $locationId = 0;

            // warehouse appliance
            (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();
            $locations_list = $this->saia_get_locations_list($post_id);

            $origin_address = $saia_res_inst->saia_multi_warehouse($locations_list, $freight_zipcode);
            // custom work for warehouse appliance
            $en_check_action_warehouse_appliance = apply_filters('en_check_action_warehouse_appliance' , FALSE);
            if($en_check_action_warehouse_appliance) {
                $origin_address = apply_filters('en_warehouse_appliance_wharehouse_check', $origin_address, 'saia');
            }

            $locationId = (isset($origin_address['id'])) ? $origin_address['id'] : $origin_address['locationId'];

            // Micro Warehouse
            (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();
            $this->products[] = $post_id;

            // Standard Packaging
            $ppp_product_pallet = [];
            $values = apply_filters('en_ppp_request', $values, $values, $_product);
            if (isset($values['ppp']) && !empty($values['ppp'])) {
                $ppp_product_pallet = $values['ppp'];
            }

            $ship_as_own_pallet = $vertical_rotation_for_pallet = 'no';
            if (!$en_ppp_pallet_product) {
                $ppp_product_pallet = [];
            }

            extract($ppp_product_pallet);

            $saia_package[$locationId]['origin'] = $origin_address;
            $getFreightClassAndHazardous = $this->saia_get_freight_class_hazardous($_product, $values['variation_id'], $values['product_id']);

            // Hazardous Material
            $hm_plan = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'hazardous_material');
            $hm_status = (!is_array($hm_plan) && $getFreightClassAndHazardous['hazardous_material'] == 'yes') ? TRUE : FALSE;

            ($getFreightClassAndHazardous["freightClass_ltl_gross"] == 'Null') ? $getFreightClassAndHazardous["freightClass_ltl_gross"] = "" : "";
            $product_markup = $this->saia_get_product_level_markup($_product, $values['variation_id'], $values['product_id'], $values['quantity']);

            if (!empty($origin_address)) {
                $locationId = (isset($origin_address['id'])) ? $origin_address['id'] : $origin_address['locationId'];
                $saia_package[$locationId]['origin'] = $origin_address;
                if (!$_product->is_virtual()) {

                    $nested_material = $this->en_nested_material($values, $_product);

                    if ($nested_material == "yes") {
                        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
                        $nestedPercentage = get_post_meta($post_id, '_nestedPercentage', true);
                        $nestedDimension = get_post_meta($post_id, '_nestedDimension', true);
                        $nestedItems = get_post_meta($post_id, '_maxNestedItems', true);
                        $StakingProperty = get_post_meta($post_id, '_nestedStakingProperty', true);
                    }

                    // Shippable handling units
                    $lineItemPalletFlag = $lineItemPackageCode = $lineItemPackageType = '0';
                    extract($shippable);

                    $product_title = str_replace(array("'", '"'), '', $_product->get_title());
                    $en_items = array(
                        'productId' => $parent_id,
                        'productName' => str_replace(array("'", '"'), '', $_product->get_name()),
                        'productQty' => $values['quantity'],
                        'product_name' => $values['quantity'] . " x " . $product_title,
                        'productPrice' => $_product->get_price(),
                        'productWeight' => $product_weight,
                        'productLength' => $length,
                        'productWidth' => $width,
                        'productHeight' => $height,
                        'productClass' => $getFreightClassAndHazardous["freightClass_ltl_gross"],
                        'freightClass' => $freightClass,
                        'hazardousMaterial' => $hm_status,
                        'hazardous_material' => $hm_status,
                        'hazmat' => $hm_status,
                        'productType' => ($_product->get_type() == 'variation') ? 'variant' : 'simple',
                        'productSku' => $_product->get_sku(),
                        'actualProductPrice' => $_product->get_price(),
                        'attributes' => $_product->get_attributes(),
                        'variantId' => ($_product->get_type() == 'variation') ? $_product->get_id() : '',

                        // Nesting
                        'nestedMaterial' => $nested_material,
                        'nestedPercentage' => $nestedPercentage,
                        'nestedDimension' => $nestedDimension,
                        'nestedItems' => $nestedItems,
                        'stakingProperty' => $StakingProperty,

                        // Shippable handling units
                        'lineItemPalletFlag' => $lineItemPalletFlag,
                        'lineItemPackageCode' => $lineItemPackageCode,
                        'lineItemPackageType' => $lineItemPackageType,

                        // Standard Packaging
                        'ship_as_own_pallet' => $ship_as_own_pallet,
                        'vertical_rotation_for_pallet' => $vertical_rotation_for_pallet,
                        'markup' => $product_markup
                    );

                    // Hook for flexibility adding to package
                    $en_items = apply_filters('en_group_package', $en_items, $values, $_product);
                    // NMFC Number things
                    $en_items = $this->en_group_package($en_items, $values, $_product);
                    // warehouse appliance
                    $en_items = apply_filters('get_warehouse_appliance_handling_fee', $en_items, $post_id);

                    $saia_package[$locationId]['items'][$counter] = $en_items;

                    // Micro Warehouse
                    $items[$post_id] = $en_items;

                    $validateProductParamsRtrn = $this->validateProductParams($saia_package[$locationId]['items'][$counter]);
                    (isset($validateProductParamsRtrn) && ($validateProductParamsRtrn === 1)) ? $validShipmentForLtl = 1 : "";
                    $saia_package[$locationId]['items'][$counter]['validForLtl'] = $validateProductParamsRtrn;
                }
            }

            $saia_enable = $this->get_saia_enable($_product);

            // Micro Warehouse
            $items_shipment[$post_id] = $saia_enable;

            $exceedWeight = get_option('en_plugins_return_LTL_quotes');
            $saia_package[$locationId]['shipment_weight'] = isset($saia_package[$locationId]['shipment_weight']) ? $saia_package[$locationId]['shipment_weight'] + $weight : $weight;
            $saia_package[$locationId]['hazardousMaterial'] = isset($saia_package[$locationId]['hazardousMaterial']) && $saia_package[$locationId]['hazardousMaterial'] == 'yes' ? $saia_package[$locationId]['hazardousMaterial'] : $getFreightClassAndHazardous["hazardous_material"];

            $validShipmentForLtl = (isset($validShipmentForLtl)) ? $validShipmentForLtl : '';
            $saia_package[$locationId]['validShipmentForLtl'] = $validShipmentForLtl;
            (isset($validShipmentForLtl) && ($validShipmentForLtl === 1)) ? $this->ValidShipments = 1 : "";

            $smallPluginExist = 0;
            $calledMethod = [];
            $eniturePluigns = json_decode(get_option('EN_Plugins'));

            if (!empty($eniturePluigns)) {
                foreach ($eniturePluigns as $enIndex => $enPlugin) {
                    $freightSmallClassName = 'WC_' . $enPlugin;

                    if (!in_array($freightSmallClassName, $calledMethod)) {
                        if (class_exists($freightSmallClassName)) {
                            $smallPluginExist = 1;
                        }
                        $calledMethod[] = $freightSmallClassName;
                    }
                }
            }

            if ($saia_enable == true || ($saia_package[$locationId]['shipment_weight'] > $weight_threshold && $exceedWeight == 'yes')) {
                $saia_package[$locationId]['saia'] = 1;
                $this->hasLTLShipment = 1;
                $this->ValidShipmentsArrSaia[] = "ltl_freight"; //$freightClass;
            } elseif (isset($saia_package[$locationId]['saia'])) {
                $saia_package[$locationId]['saia'] = 1;
                $this->hasLTLShipment = 1;
                $this->ValidShipmentsArrSaia[] = "ltl_freight"; //$freightClass;
            } elseif ($smallPluginExist == 1) {
                $saia_package[$locationId]['small'] = 1;
                $this->ValidShipmentsArrSaia[] = "small_shipment"; //$freightClass;
            } else {
                $this->ValidShipmentsArrSaia[] = "no_shipment";
            }

            $counter++;
        }

        // Micro Warehouse
        $eniureLicenceKey = get_option('wc_settings_saia_plugin_licence_key');
        $saia_package = apply_filters('en_micro_warehouse', $saia_package, $this->products, $this->dropship_location_array, $this->destination_Address_saia, $this->origin, $smallPluginExist, $items, $items_shipment, $this->warehouse_products, $eniureLicenceKey, 'saia');
        // Eniture debug mood
        do_action("eniture_debug_mood", "Product Detail (SAIA)", $saia_package);
        return $saia_package;
    }

    /**
     * Set images urls | Images for FDO
     * @param array type $en_fdo_image_urls
     * @return array type
     */
    public function en_fdo_image_urls_merge($en_fdo_image_urls)
    {
        return array_merge($this->en_fdo_image_urls, $en_fdo_image_urls);
    }

    /**
     * Get images urls | Images for FDO
     * @param array type $values
     * @param array type $_product
     * @return array type
     */
    public function en_fdo_image_urls($values, $_product)
    {
        $product_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        $gallery_image_ids = $_product->get_gallery_image_ids();
        foreach ($gallery_image_ids as $key => $image_id) {
            $gallery_image_ids[$key] = $image_id > 0 ? wp_get_attachment_url($image_id) : '';
        }

        $image_id = $_product->get_image_id();
        $this->en_fdo_image_urls[$product_id] = [
            'product_id' => $product_id,
            'image_id' => $image_id > 0 ? wp_get_attachment_url($image_id) : '',
            'gallery_image_ids' => $gallery_image_ids
        ];

        add_filter('en_fdo_image_urls_merge', [$this, 'en_fdo_image_urls_merge'], 10, 1);
    }

    /**
     * Nested Material
     * @param array type $values
     * @param array type $_product
     * @return string type
     */
    function en_nested_material($values, $_product)
    {
        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        return get_post_meta($post_id, '_nestedMaterials', true);
    }

    /**
     *
     * @param type $productData
     * @return int
     */
    function validateProductParams($productData)
    {
        if ((!isset($productData['freightClass']) || $productData['freightClass'] != "ltl_freight")) {
            return 0;
        }
        return 1;
    }

    /**
     * Check enable_dropship and get Locations list
     * @param $post_id
     * @return Shipping Class
     * @global $wpdb
     */
    function saia_get_locations_list($post_id)
    {
        global $wpdb;

        $locations_list = [];

        (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id;
        $enable_dropship = get_post_meta($post_id, '_enable_dropship', true);
        if ($enable_dropship == 'yes') {
            $get_loc = get_post_meta($post_id, '_dropship_location', true);
            if ($get_loc == '') {
                // Micro Warehouse
                $this->warehouse_products[] = $post_id;
                return array('error' => 'saia dp location not found!');
            }

            // Multi Dropship
            $multi_dropship = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'multi_dropship');

            if (is_array($multi_dropship)) {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship' LIMIT 1"
                );
            } else {
                $get_loc = ($get_loc !== '') ? maybe_unserialize($get_loc) : $get_loc;
                $get_loc = is_array($get_loc) ? implode(" ', '", $get_loc) : $get_loc;
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE id IN ('" . $get_loc . "')"
                );
            }

            // Micro Warehouse
            $this->multiple_dropship_of_prod($locations_list, $post_id);
            $eniture_debug_name = "Dropships";
        }

        if (empty($locations_list)) {
            // Multi Warehouse
            $multi_warehouse = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'multi_warehouse');

            if (is_array($multi_warehouse)) {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse' LIMIT 1"
                );
            } else {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse'"
                );
            }

            // Micro Warehouse
            $this->warehouse_products[] = $post_id;
            $eniture_debug_name = "Warehouses";
        }

        do_action("eniture_debug_mood", "Quotes $eniture_debug_name (s)", $locations_list);
        return $locations_list;
    }

    /**
     * Get Freight Class and Hazardous Material Checkbox
     * @param $_product
     * @param $variation_id
     * @param $product_id
     * @return Shipping Class
     */
    function saia_get_freight_class_hazardous($_product, $variation_id, $product_id)
    {
        if ($_product->get_type() == 'variation') {
            $hazardous_material = get_post_meta($variation_id, '_hazardousmaterials', true);
            $variation_class = get_post_meta($variation_id, '_ltl_freight_variation', true);

            //get_parent condition for Freight class
            if ($variation_class == 'get_parent') {
                $variation_class = get_post_meta($product_id, '_ltl_freight', true);
                $freightClass_ltl_gross = $variation_class;
            } else {
                if(!empty($variation_class)){
                    $freightClass_ltl_gross = $variation_class;
                }else{
                    $freightClass_ltl_gross = get_post_meta($variation_id, '_ltl_freight', true);

                    if(empty($freightClass_ltl_gross)){
                        $freightClass_ltl_gross = get_post_meta($product_id, '_ltl_freight', true);
                    }
                }
            }
        } else {
            $hazardous_material = get_post_meta($_product->get_id(), '_hazardousmaterials', true);
            $freightClass_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
        }
        $aDataArr = array(
            'freightClass_ltl_gross' => $freightClass_ltl_gross,
            'hazardous_material' => $hazardous_material
        );

        return $aDataArr;
    }

    // Micro Warehouse
    public function multiple_dropship_of_prod($locations_list, $post_id)
    {
        $post_id = (string)$post_id;

        foreach ($locations_list as $key => $value) {
            $dropship_data = $this->address_array($value);

            $this->origin["D" . $dropship_data['zip']] = $dropship_data;
            if (!isset($this->dropship_location_array["D" . $dropship_data['zip']]) || !in_array($post_id, $this->dropship_location_array["D" . $dropship_data['zip']])) {
                $this->dropship_location_array["D" . $dropship_data['zip']][] = $post_id;
            }
        }

    }

    // Micro Warehouse
    public function address_array($value)
    {
        $dropship_data = [];

        $dropship_data['locationId'] = (isset($value->id)) ? $value->id : "";
        $dropship_data['zip'] = (isset($value->zip)) ? $value->zip : "";
        $dropship_data['city'] = (isset($value->city)) ? $value->city : "";
        $dropship_data['state'] = (isset($value->state)) ? $value->state : "";
        // Origin terminal address
        $dropship_data['address'] = (isset($value->address)) ? $value->address : "";
        // Terminal phone number
        $dropship_data['phone_instore'] = (isset($value->phone_instore)) ? $value->phone_instore : "";
        $dropship_data['location'] = (isset($value->location)) ? $value->location : "";
        $dropship_data['country'] = (isset($value->country)) ? $value->country : "";
        $dropship_data['enable_store_pickup'] = (isset($value->enable_store_pickup)) ? $value->enable_store_pickup : "";
        $dropship_data['fee_local_delivery'] = (isset($value->fee_local_delivery)) ? $value->fee_local_delivery : "";
        $dropship_data['suppress_local_delivery'] = (isset($value->suppress_local_delivery)) ? $value->suppress_local_delivery : "";
        $dropship_data['miles_store_pickup'] = (isset($value->miles_store_pickup)) ? $value->miles_store_pickup : "";
        $dropship_data['match_postal_store_pickup'] = (isset($value->match_postal_store_pickup)) ? $value->match_postal_store_pickup : "";
        $dropship_data['checkout_desc_store_pickup'] = (isset($value->checkout_desc_store_pickup)) ? $value->checkout_desc_store_pickup : "";
        $dropship_data['enable_local_delivery'] = (isset($value->enable_local_delivery)) ? $value->enable_local_delivery : "";
        $dropship_data['miles_local_delivery'] = (isset($value->miles_local_delivery)) ? $value->miles_local_delivery : "";
        $dropship_data['match_postal_local_delivery'] = (isset($value->match_postal_local_delivery)) ? $value->match_postal_local_delivery : "";
        $dropship_data['checkout_desc_local_delivery'] = (isset($value->checkout_desc_local_delivery)) ? $value->checkout_desc_local_delivery : "";

        $dropship_data['sender_origin'] = $dropship_data['location'] . ": " . $dropship_data['city'] . ", " . $dropship_data['state'] . " " . $dropship_data['zip'];

        return $dropship_data;
    }

    /**
     * Get SAIA Enable or not
     * @param $_product
     * @return Shipping Class
     */
    function get_saia_enable($_product)
    {
        if ($_product->get_type() == 'variation') {
            $ship_class_id = $_product->get_shipping_class_id();

            if ($ship_class_id == 0) {
                $parent_data = $_product->get_parent_data();
                $get_parent_term = get_term_by('id', $parent_data['shipping_class_id'], 'product_shipping_class');
                $get_shipping_result = (isset($get_parent_term->slug)) ? $get_parent_term->slug : '';
            } else {
                $get_shipping_result = $_product->get_shipping_class();
            }

            $saia_enable = ($get_shipping_result && $get_shipping_result == 'ltl_freight') ? true : false;
        } else {
            $get_shipping_result = $_product->get_shipping_class();
            $saia_enable = ($get_shipping_result == 'ltl_freight') ? true : false;
        }

        return $saia_enable;
    }

    /**
     * Grouping For Shipment Quotes
     * @param $quotes
     * @param $handlng_fee
     * @return Total Cost
     */
    function saia_grouped_quotes($quotes, $handlng_fee)
    {
        $totalPrice = 0;
        $grandTotal = 0;
        $grandTotalWdoutLiftGate = 0;
        $label_sfx_arr = "";
        $freight = [];

//      Minify Me 
        $total_shipment = [];

        if (count($quotes) > 0 && !empty($quotes)) {
            foreach ($quotes as $multiValues) {
                if (isset($multiValues['cost']) && !empty($multiValues['cost'])) {

                    $totalPriceLiftGate = (isset($multiValues['surcharges']['liftgateFee'])) ? $multiValues['surcharges']['liftgateFee'] : 0;
                    $sender_zip = (isset($multiValues['sender_zip'])) ? $multiValues['sender_zip'] : '';
                    $sender_origin = (isset($multiValues['sender_origin'])) ? $multiValues['sender_origin'] : '';
                    $product_name = (isset($multiValues['product_name'])) ? $multiValues['product_name'] : [];
                    $accessorials = (isset($multiValues['accessorials'])) ? $multiValues['accessorials'] : [];
                    $sender_location = (isset($multiValues['sender_location'])) ? $multiValues['sender_location'] : '';

                    if ($handlng_fee != '') {
                        $simple_cost = $this->saia_parse_handeling_fee($handlng_fee, $multiValues['cost']);
                        $total_shipment['simple'][] = array('cost' => $simple_cost, 'sender_zip' => $sender_zip, 'sender_origin' => $sender_origin, 'product_name' => $product_name, 'accessorials' => $accessorials, 'sender_location' => $sender_location, 'plugin_name' => 'saia');
                        $grandTotal += $simple_cost;

                        $other_cost = $this->saia_parse_handeling_fee($handlng_fee, ($multiValues['cost'] - $totalPriceLiftGate));
                        $total_shipment['other'][] = array('cost' => $other_cost, 'sender_zip' => $sender_zip, 'sender_origin' => $sender_origin, 'product_name' => $product_name, 'accessorials' => $accessorials, 'sender_location' => $sender_location, 'plugin_name' => 'saia');
                        $grandTotalWdoutLiftGate += $other_cost;
                    } else {
                        $simple_cost = $multiValues['cost'];
                        $total_shipment['simple'][] = array('cost' => $simple_cost, 'sender_zip' => $sender_zip, 'sender_origin' => $sender_origin, 'product_name' => $product_name, 'accessorials' => $accessorials, 'sender_location' => $sender_location, 'plugin_name' => 'saia');
                        $grandTotal += $simple_cost;

                        $other_cost = $multiValues['cost'] - $totalPriceLiftGate;
                        $total_shipment['other'][] = array('cost' => $other_cost, 'sender_zip' => $sender_zip, 'sender_origin' => $sender_origin, 'product_name' => $product_name, 'accessorials' => $accessorials, 'sender_location' => $sender_location, 'plugin_name' => 'saia');
                        $grandTotalWdoutLiftGate += $other_cost;
                    }

                    (isset($multiValues['label_sfx_arr'])) ? $label_sfx_arr = $multiValues['label_sfx_arr'] : '';
                } else {
                    $this->errors = 'no quotes return';
                    continue;
                }
            }
        }

        $freight = array(
            'totals' => $grandTotal,
            'label_sfx_arr' => $label_sfx_arr,
            'grandTotalWdoutLiftGate' => $grandTotalWdoutLiftGate,
            'total_shipment' => $total_shipment,
        );

        return $freight;
    }

    /**
     * Grouping For Small Quotes
     * @param $smallQuotes
     * @return Total Cost
     */
    function saia_get_small_packages_cost($smallQuotes)
    {
        $result = [];
        $minCostArr = [];

        if (isset($smallQuotes) && count($smallQuotes) > 0) {
            foreach ($smallQuotes as $smQuotes) {
                $CostArr = [];
                if (!isset($smQuotes['error'])) {
                    foreach ($smQuotes as $smQuote) {
                        $CostArr[] = $smQuote['cost'];
                        $result['error'] = false;
                    }
                    $minCostArr[] = (count($CostArr) > 0) ? min($CostArr) : "";
                } else {
                    $result['error'] = !isset($result['error']) ? true : $result['error'];
                }
            }
            $result['price'] = (isset($minCostArr) && count($minCostArr) > 0) ? min($minCostArr) : "";
        } else {
            $result['error'] = false;
            $result['price'] = 0;
        }
        return $result;
    }

    /**
     * Calculate Handling Fee
     * @param $handlng_fee
     * @param $cost
     * @return int
     */
    function saia_parse_handeling_fee($handlng_fee, $cost)
    {
        $pos = strpos($handlng_fee, '%');
        if ($pos > 0) {
            $rest = substr($handlng_fee, $pos);
            $exp = explode($rest, $handlng_fee);
            $get = $exp[0];
            $percnt = $get / 100 * $cost;
            $grandTotal = $cost + $percnt;
        } else {
            $grandTotal = $cost + $handlng_fee;
        }
        return $grandTotal;
    }

    /**
     * parameters $_product
     * return $dimensions in inches
     */
    function dimensions_conversion($_product)
    {
        $dimension_unit = get_option('woocommerce_dimension_unit');
        $dimensions = [];

        switch ($dimension_unit) {

            case 'ft':
                $dimensions['height'] = round((float) $_product->get_height() * 12, 2);
                $dimensions['width'] = round((float) $_product->get_width() * 12, 2);
                $dimensions['length'] = round((float) $_product->get_length() * 12, 2);
                break;

            case 'cm':
                $dimensions['height'] = round((float) $_product->get_height() * 0.3937007874, 2);
                $dimensions['width'] = round((float) $_product->get_width() * 0.3937007874, 2);
                $dimensions['length'] = round((float) $_product->get_length() * 0.3937007874, 2);
                break;

            case 'mi':
                $dimensions['height'] = round((float) $_product->get_height() * 63360, 2);
                $dimensions['width'] = round((float) $_product->get_width() * 63360, 2);
                $dimensions['length'] = round((float) $_product->get_length() * 63360, 2);
                break;

            case 'km':
                $dimensions['height'] = round((float) $_product->get_height() * 39370.1, 2);
                $dimensions['width'] = round((float) $_product->get_width() * 39370.1, 2);
                $dimensions['length'] = round((float) $_product->get_length() * 39370.1, 2);
                break;
        }

        return $dimensions;
    }

    /**
     * Get the product nmfc number
     */
    public function en_group_package($item, $product_object, $product_detail)
    {
        $en_nmfc_number = $this->en_nmfc_number($product_object, $product_detail);
        $item['nmfc_number'] = $en_nmfc_number;
        return $item;
    }

    /**
     * Get product shippable unit enabled
     */
    public function en_nmfc_number($product_object, $product_detail)
    {
        $post_id = (isset($product_object['variation_id']) && $product_object['variation_id'] > 0) ? $product_object['variation_id'] : $product_detail->get_id();
        return get_post_meta($post_id, '_nmfc_number', true);
    }

    /**
     * Returns product level markup
     */
    public function saia_get_product_level_markup($_product, $variation_id, $product_id, $quantity)
    {
        $product_level_markup = 0;
        if ($_product->get_type() == 'variation') {
            $product_level_markup = get_post_meta($variation_id, '_en_product_markup_variation', true);
            if(empty($product_level_markup) || $product_level_markup == 'get_parent'){
                $product_level_markup = get_post_meta($_product->get_id(), '_en_product_markup', true);
            }
        } else {
            $product_level_markup = get_post_meta($_product->get_id(), '_en_product_markup', true);
        }

        if(empty($product_level_markup)) {
            $product_level_markup = get_post_meta($product_id, '_en_product_markup', true);
        }

        if(!empty($product_level_markup) && strpos($product_level_markup, '%') === false && is_numeric($product_level_markup) && is_numeric($quantity)){
            $product_level_markup *= $quantity;
        }else if(!empty($product_level_markup) && strpos($product_level_markup, '%') > 0 && is_numeric($quantity)){

            $position = strpos($product_level_markup, '%');
            $first_str = substr($product_level_markup, $position);
            $arr = explode($first_str, $product_level_markup);
            $percentage_value = $arr[0];
            $product_price = $_product->get_price();
            if(!empty($product_price)){
                $product_level_markup = $percentage_value / 100 * ($product_price * $quantity);
            }else{
                $product_level_markup = 0;
            }
        }

        return $product_level_markup;
    }

    /**
     * Returns flat rate price and quantity
     */
    function en_get_flat_rate_price($values, $_product)
    {
        if ($_product->get_type() == 'variation') {
            $flat_rate_price = get_post_meta($values['variation_id'], 'en_flat_rate_price', true);
            if (strlen($flat_rate_price) < 1) {
                $flat_rate_price = get_post_meta($values['product_id'], 'en_flat_rate_price', true);
            }
        } else {
            $flat_rate_price = get_post_meta($_product->get_id(), 'en_flat_rate_price', true);
        }

        return $flat_rate_price;
    }
}
