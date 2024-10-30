<?php

/**
 * SAIA WooComerce | Setting Tab Class
 * @package     Woocommerce SAIA Edition
 * @author      <https://eniture.com/>
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SAIA WooComerce | Setting Tab Class
 */
class SAIA_Freight_Settings extends WC_Settings_Page {

    /**
     * Setting Tab Class Constructor
     */
    public function __construct() {
        $this->id = 'saia_quotes';
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
        add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
        add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
    }

    /**
     * SAIA Setting Tab For WooCommerce
     * @param $settings_tabs
     * @return seetings
     */
    public function add_settings_tab($settings_tabs) {
        $settings_tabs[$this->id] = __('SAIA Freight', 'woocommerce_saia_quote');
        return $settings_tabs;
    }

    /**
     * SAIA Setting Sections
     * @return array
     */
    public function get_sections() {
        $sections = array(
            '' => __('Connection Settings', 'woocommerce_saia_quote'),
            'section-1' => __('Quote Settings', 'woocommerce_saia_quote'),
            'section-2' => __('Warehouses', 'woocommerce_saia_quote'),
            // fdo va
            'section-4' => __('FreightDesk Online', 'woocommerce_saia_quote'),
            'section-5' => __('Validate Addresses', 'woocommerce_saia_quote'),
            'section-3' => __('User Guide', 'woocommerce_saia_quote'),
        );

        // Logs data
        $enable_logs = get_option('enale_logs_saia');
        if ($enable_logs == 'yes') {
            $sections['en-logs'] = 'Logs';
        }

        $sections = apply_filters('en_woo_addons_sections', $sections, en_woo_plugin_saia_quotes);
        // Standard Packaging
        $sections = apply_filters('en_woo_pallet_addons_sections', $sections, en_woo_plugin_saia_quotes);
        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * SAIA Warehouse Tab
     */
    public function saia_warehouse() {
        require_once 'warehouse-dropship/wild/warehouse/saia_warehouse_template.php';
        require_once 'warehouse-dropship/wild/dropship/saia_dropship_template.php';
    }

    /**
     * SAIA User Guide Tab
     */
    public function saia_user_guide() {
        include_once( 'template/guide.php' );
    }

    /**
     * SAIA Pages Initialize
     * @param $section
     * @return array
     */
    public function get_settings($section = null) {
        ob_start();
        switch ($section) {
            case 'section-0' :
                $settings = SAIA_Connection_Settings::saia_con_setting();
                break;
            case 'section-1':
                $saia_quote_Settings = new SAIA_Quote_Settings();
                $settings = $saia_quote_Settings->saia_quote_settings_tab();
                break;
            case 'section-2' :
                $this->saia_warehouse();
                $settings = array();
                break;
            case 'section-3' :
                $this->saia_user_guide();
                $settings = array();
                break;
            // fdo va
            case 'section-4' :
                $this->freightdesk_online_section();
                $settings = [];
                break;

            case 'section-5' :
                $this->validate_addresses_section();
                $settings = [];
                break;
                
            case 'en-logs' :
                $this->shipping_logs_section();
                $settings = [];
                break;

            default:
                $saia_con_settings = new SAIA_Connection_Settings();
                $settings = $saia_con_settings->saia_con_setting();

                break;
        }

        $settings = apply_filters('en_woo_addons_settings', $settings, $section, en_woo_plugin_saia_quotes);
        // Standard Packaging
        $settings = apply_filters('en_woo_pallet_addons_settings', $settings, $section, en_woo_plugin_saia_quotes);
        $settings = $this->avaibility_addon($settings);
        return apply_filters('woocommerce_saia_quote', $settings, $section);
    }

    /**
     * avaibility_addon 
     * @param array type $settings
     * @return array type
     */
    function avaibility_addon($settings) {
        if (is_plugin_active('residential-address-detection/residential-address-detection.php')) {
            unset($settings['avaibility_lift_gate']);
            unset($settings['avaibility_auto_residential']);
        }

        return $settings;
    }

    /**
     * SAIA Settings Pages Output
     * @global $current_section
     */
    public function output() {
        global $current_section;
        $settings = $this->get_settings($current_section);
        WC_Admin_Settings::output_fields($settings);
    }

    /**
     * SAIA Save Settings
     * @global $current_section
     */
    public function save() {
        global $current_section;
        $settings = $this->get_settings($current_section);
        WC_Admin_Settings::save_fields($settings);
    }
    // fdo va
    /**
     * FreightDesk Online section
     */
    public function freightdesk_online_section()
    {
        include_once plugin_dir_path(__FILE__) . 'fdo/freightdesk-online-section.php';
    }

    /**
     * Validate Addresses Section
     */
    public function validate_addresses_section()
    {
        include_once plugin_dir_path(__FILE__) . 'fdo/validate-addresses-section.php';
    }

    /**
     * Shipping Logs Section
    */
    public function shipping_logs_section()
    {
        include_once plugin_dir_path(__FILE__) . 'logs/en-logs.php';
    }

}

return new SAIA_Freight_Settings();
