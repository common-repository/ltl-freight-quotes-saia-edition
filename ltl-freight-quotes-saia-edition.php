<?php
/**
 * Plugin Name:    LTL Freight Quotes - SAIA Edition
 * Plugin URI:     https://eniture.com/products/
 * Description:    Dynamically retrieves your negotiated shipping rates from SAIA Freight and displays the results in the WooCommerce shopping cart.
 * Version:        2.2.9
 * Author:         Eniture Technology
 * Author URI:     http://eniture.com/
 * Text Domain:    eniture-technology
 * License:        GPL version 2 or later - http://www.eniture.com/
 * WC requires at least: 6.4
 * WC tested up to: 9.1.4
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('SAIA_HITTING_DOMAIN_URL', 'https://ws059.eniture.com');
define('SAIA_FDO_HITTING_URL', 'https://freightdesk.online/api/updatedWoocomData');
define('SAIA_MAIN_FILE', __FILE__);

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

// Define reference
function en_saia_freight_plugin($plugins)
{
    $plugins['lfq'] = (isset($plugins['lfq'])) ? array_merge($plugins['lfq'], ['saia' => 'SAIA_Freight_Shipping_Class']) : ['saia' => 'SAIA_Freight_Shipping_Class'];
    return $plugins;
}

add_filter('en_plugins', 'en_saia_freight_plugin');
if (!function_exists('en_woo_plans_notification_PD')) {

    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for ($e = 1; $e <= 25; $e++) {
            $settings = get_option($eniture_plugins_id . $e);
            if (isset($settings) && (!empty($settings)) && (is_array($settings))) {
                $plugin_detail = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if ($key != 'plugin_name') {
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? ", $plugin_name" : "$plugin_name";
                    }
                }
            }
        }

        return $product_detail_options;
    }

    add_filter('en_woo_plans_notification_action', 'en_woo_plans_notification_PD', 10, 1);
}

if (!function_exists('en_woo_plans_notification_message')) {

    function en_woo_plans_notification_message($enable_plugins, $disable_plugins)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0) ? " $disable_plugins: Upgrade to <b>Standard Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_notification_message_action', 'en_woo_plans_notification_message', 10, 2);
}

// Product detail set plans notification message for nested checkbox
if (!function_exists('en_woo_plans_nested_notification_message')) {

    function en_woo_plans_nested_notification_message($enable_plugins, $disable_plugins, $feature)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0 && $feature == 'nested_material') ? " $disable_plugins: Upgrade to <b>Advance Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_nested_notification_message_action', 'en_woo_plans_nested_notification_message', 10, 3);
}

if (!function_exists('is_plugin_active')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * Load scripts for SAIA Freight json tree view
 */
if (!function_exists('en_saia_jtv_script')) {
    function en_saia_jtv_script()
    {
        wp_register_style('en_saia_json_tree_view_style', plugin_dir_url(__FILE__) . 'logs/en-json-tree-view/en-jtv-style.css');
        wp_register_script('en_saia_json_tree_view_script', plugin_dir_url(__FILE__) . 'logs/en-json-tree-view/en-jtv-script.js', ['jquery'], '1.0.0');

        wp_enqueue_style('en_saia_json_tree_view_style');
        wp_enqueue_script('en_saia_json_tree_view_script', [
            'en_tree_view_url' => plugins_url(),
        ]);
    }

    add_action('admin_init', 'en_saia_jtv_script');
}

if (!is_plugin_active('woocommerce/woocommerce.php')) {
    add_action('admin_notices', 'saia_wc_avaibility_error');
}

/**
 * Check WooCommerce installlation
 */
function saia_wc_avaibility_error()
{
    $class = "error";
    $message = "LTL Freight Quotes - SAIA Edition is enabled, but not effective. It requires WooCommerce in order to work, please <a target='_blank' href='https://wordpress.org/plugins/woocommerce/installation/'>Install</a> WooCommerce Plugin. Reactivate LTL Freight Quotes - SAIA Edition plugin to create LTL shipping class.";
    echo "<div class=\"$class\"> <p>$message</p></div>";
}

add_action('admin_init', 'saia_check_wc_version');

/**
 * Check WooCommerce version compatibility
 */
function saia_check_wc_version()
{
    $woo_version = saia_wc_version_number();
    $version = '2.6';
    if (!version_compare($woo_version, $version, ">=")) {
        add_action('admin_notices', 'wc_version_incompatibility_saia');
    }
}

/**
 * Check WooCommerce version incompatibility
 */
function wc_version_incompatibility_saia()
{
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            _e('LTL Freight Quotes - SAIA Edition plugin requires WooCommerce version 2.6 or higher to work. Functionality may not work properly.', 'wwe-woo-version-failure');
            ?>
        </p>
    </div>
    <?php
}

/**
 * WooCommerce version
 * @return version
 */
function saia_wc_version_number()
{
    $plugin_folder = get_plugins('/' . 'woocommerce');
    $plugin_file = 'woocommerce.php';

    if (isset($plugin_folder[$plugin_file]['Version'])) {
        return $plugin_folder[$plugin_file]['Version'];
    } else {
        return NULL;
    }
}

add_action('admin_enqueue_scripts', 'en_saia_script');

/**
 * Load Front-end scripts for saia
 */
function en_saia_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_saia_script', plugin_dir_url(__FILE__) . 'js/saia.js', array(), '1.0.9');
    wp_localize_script('en_saia_script', 'en_saia_admin_script', array(
        'plugins_url' => plugins_url(),
        'allow_proceed_checkout_eniture' => trim(get_option("allow_proceed_checkout_eniture")),
        'prevent_proceed_checkout_eniture' => trim(get_option("prevent_proceed_checkout_eniture")),
    ));
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) || is_plugin_active_for_network('woocommerce/woocommerce.php')) {

    add_action('admin_enqueue_scripts', 'saia_admin_script');

    /**
     * Load scripts for SAIA
     */
    function saia_admin_script()
    {
        wp_register_style('saia-style', plugin_dir_url(__FILE__) . '/css/saia-style.css', false, '1.0.9');
        wp_enqueue_style('saia-style');
    }

    /**
     * Inlude Plugin Files
     */
    require_once 'fdo/en-fdo.php';
    require_once 'template/csv-export.php';
    require_once('warehouse-dropship/saia-wild-delivery.php');
    require_once('warehouse-dropship/get-distance-request.php');

    require_once 'template/products-nested-options.php';

    require_once('saia-liftgate-as-option.php');
    require_once('saia-test-connection.php');
    require_once('saia-shipping-class.php');
    require_once('db/saia-db.php');
    require_once('saia-admin-filter.php');
    require_once('saia-group-package.php');
    require_once('saia-carrier-service.php');
    require_once('template/connection-settings.php');
    require_once('template/quote-settings.php');
    require_once('saia-wc-update-change.php');
    require_once('saia-curl-class.php');

    require_once 'order/en-order-export.php';

    $en_hide_widget = apply_filters('en_hide_widget_for_this_carrier', false);
    if (!$en_hide_widget) {
        require_once 'order/en-order-widget.php';
    }

    // Origin terminal address
    add_action('admin_init', 'saia_freight_update_warehouse');

    require_once('product/en-product-detail.php');

    require_once('standard-package-addon/standard-package-addon.php');
    require_once 'update-plan.php';
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    register_activation_hook(__FILE__, 'create_saia_ltl_freight_class');
    register_activation_hook(__FILE__, 'create_saia_wh_db');
    register_activation_hook(__FILE__, 'create_saia_option');

    register_activation_hook(__FILE__, 'saia_quotes_activate_hit_to_update_plan');
    register_deactivation_hook(__FILE__, 'saia_quotes_deactivate_hit_to_update_plan');
    register_deactivation_hook(__FILE__, 'en_saia_deactivate_plugin');

    /**
     * SAIA Action And Filters
     */
    add_action('woocommerce_shipping_init', 'saia_logistics_init');
    add_filter('woocommerce_shipping_methods', 'add_saia_logistics');
    add_filter('woocommerce_get_settings_pages', 'saia_shipping_sections');
    add_filter('woocommerce_package_rates', 'saia_hide_shipping', 99);
    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');

    add_filter('plugin_action_links', 'saia_logistics_add_action_plugin', 10, 5);
    /* Custom Error Message */
    add_filter('woocommerce_cart_no_shipping_available_html', 'saia_default_error_message', 999, 1);
    add_action('init', 'saia_no_method_available');

    add_action('init', 'saia_default_error_message_selection');

    /**
     * Update Default custom error message selection
     */
    function saia_default_error_message_selection()
    {
        $custom_error_selection = get_option('wc_pervent_proceed_checkout_eniture');
        if (empty($custom_error_selection)) {
            update_option('wc_pervent_proceed_checkout_eniture', 'prevent', true);
            update_option('prevent_proceed_checkout_eniture', 'There are no shipping methods available for the address provided. Please check the address.', true);
        }
    }

    if (!function_exists("saia_default_error_message")) {

        function saia_default_error_message($message)
        {

            if (get_option('wc_pervent_proceed_checkout_eniture') == 'prevent') {
                remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
                return __(get_option('prevent_proceed_checkout_eniture'));
            } else if (get_option('wc_pervent_proceed_checkout_eniture') == 'allow') {
                add_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
                return __(get_option('allow_proceed_checkout_eniture'));
            }
        }

    }

    /**
     * SAIA action links
     * @staticvar $plugin
     * @param $actions
     * @param $plugin_file
     * @return links array settings
     */
    function saia_logistics_add_action_plugin($actions, $plugin_file)
    {
        static $plugin;
        if (!isset($plugin))
            $plugin = plugin_basename(__FILE__);
        if ($plugin == $plugin_file) {
            $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=saia_quotes">' . __('Settings', 'General') . '</a>');
            $site_link = array('support' => '<a href="https://support.eniture.com/" target="_blank">Support</a>');
            $actions = array_merge($settings, $actions);
            $actions = array_merge($site_link, $actions);
        }
        return $actions;
    }

    /**
     * Hook to call when plugin update
     */
    function en_saia_update_now( $upgrader_object, $options ) {
        $en_saia_path_name = plugin_basename( __FILE__ );

        if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
            foreach($options['plugins'] as $each_plugin) {
                if ($each_plugin == $en_saia_path_name) {
                    if (!function_exists('saia_quotes_activate_hit_to_update_plan')) {
                        require_once(__DIR__ . '/update-plan.php');
                    }
                    
                    saia_quotes_activate_hit_to_update_plan();
                    create_saia_wh_db();
                    create_saia_option();
                    create_saia_ltl_freight_class();
                    update_option('en_saia_update_now', $plugin_version);
                }
            }
        }
    }

    add_action( 'upgrader_process_complete', 'en_saia_update_now',10, 2);

}

define("en_woo_plugin_saia_quotes", "saia_quotes");

add_action('wp_enqueue_scripts', 'en_ltl_saia_frontend_checkout_script');

/**
 * Load Frontend scripts for saia
 */
function en_ltl_saia_frontend_checkout_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_ltl_saia_frontend_checkout_script', plugin_dir_url(__FILE__) . 'front/js/en-saia-checkout.js', array(), '1.0.0');
    wp_localize_script('en_ltl_saia_frontend_checkout_script', 'frontend_script', array(
        'pluginsUrl' => plugins_url(),
    ));
}

/**
 * Get Host
 * @param type $url
 * @return type
 */
if (!function_exists('getHost')) {

    function getHost($url)
    {
        $parseUrl = parse_url(trim($url));
        if (isset($parseUrl['host'])) {
            $host = $parseUrl['host'];
        } else {
            $path = explode('/', $parseUrl['path']);
            $host = $path[0];
        }
        return trim($host);
    }

}

/**
 * Get Domain Name
 */
if (!function_exists('saia_quotes_get_domain')) {

    function saia_quotes_get_domain()
    {
        global $wp;
        $url = home_url($wp->request);
        return getHost($url);
    }

}


/**
 * Plans Common Hooks
 */
add_filter('saia_quotes_quotes_plans_suscription_and_features', 'saia_quotes_quotes_plans_suscription_and_features', 1);

function saia_quotes_quotes_plans_suscription_and_features($feature)
{

    $package = get_option('saia_quotes_packages_quotes_package');

    $features = array
    (
        'instore_pickup_local_devlivery' => array('3'),
        'multi_warehouse' => array('2', '3'),
        'multi_dropship' => array('', '0', '1', '2', '3'),
        'hazardous_material' => array('2', '3'),
        'hold_at_terminal' => array('3'),
        'nested_material' => array('3'),
    );

    return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : $features[$feature];
}

add_filter('saia_quotes_plans_notification_link', 'saia_quotes_plans_notification_link', 1);

function saia_quotes_plans_notification_link($plans)
{
    $plans = is_array($plans) ? $plans : array();
    $plan = isset($plans) && !empty($plans) ? current($plans) : '';
    $plan_to_upgrade = "";
    switch ($plan) {
        case 2:
            $plan_to_upgrade = "<a href='https://eniture.com/eniture_subscription_dashboard/woocommerce-saia-ltl-freight/' target='_blank'>Standard Plan required</a>";
            break;
        case 3:
            $plan_to_upgrade = "<a href='https://eniture.com/eniture_subscription_dashboard/woocommerce-saia-ltl-freight/' target='_blank'>Advanced Plan required</a>";
            break;
    }

    return $plan_to_upgrade;
}

/* * *
 * Add account number field on add/edit warehouse/dropship
 */

function saia_en_append_account_number_multiple_plugins($template)
{
    $template .= ' <div class="en_wd_add_warehouse_custom_input en_wd_add_warehouse_input en_wd_saia_account_label">
                        <label for="en_wd__dropship_saia_account">SAIA Account Number</label>
                        <input type="text" data-connection_input="saia_test_connection_zipcode" data-post_input="saia_account"  title="SAIA Account Nmuber" name="en_wd_saia_account" value="" placeholder="SAIA Account Number" class="en_wd_saia_account" data-optional="1">
                        <span class="en_wd_err"></span>
                    </div>';
    return $template;
}

add_filter('en_append_account_number_multiple_plugins', 'saia_en_append_account_number_multiple_plugins', 1, 1);

/*
 * Add account number hidden field on add/edit warehouse/dropship
 */

function saia_en_append_account_number_hidden_multiple_plugins($template)
{
    $template .= '<div class="en_wd_account_number">
        <input type="hidden" data-account_num_on_warehouse="en_wd_saia_account_label" value="' . get_option('wc_settings_saia_accountnbr_postal_code') . '" id="saia_test_connection_zipcode">
    </div>';
    return $template;
}

add_filter('en_append_account_number_hidden_multiple_plugins', 'saia_en_append_account_number_hidden_multiple_plugins', 1, 1);


add_action('init', 'remove_hat_add');
function remove_hat_add() {
    $remove_hat_address = get_option('saia_ltl_hold_at_terminal_remove_address');
    if($remove_hat_address == false) {
        update_option('saia_ltl_hold_at_terminal_remove_address', 'yes');
    }
}
// fdo va
add_action('wp_ajax_nopriv_saia_fd', 'saia_fd_api');
add_action('wp_ajax_saia_fd', 'saia_fd_api');
/**
 * UPS AJAX Request
 */
function saia_fd_api()
{
    $store_name = saia_quotes_get_domain();
    $company_id = $_POST['company_id'];
    $data = [
        'plateform'  => 'wp',
        'store_name' => $store_name,
        'company_id' => $company_id,
        'fd_section' => 'tab=saia_quotes&section=section-4',
    ];
    if (is_array($data) && count($data) > 0) {
        if($_POST['disconnect'] != 'disconnect') {
            $url =  'https://freightdesk.online/validate-company';
        }else {
            $url = 'https://freightdesk.online/disconnect-woo-connection';
        }
        $response = wp_remote_post($url, [
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => $data,
            ]
        );
        $response = wp_remote_retrieve_body($response);
    }
    if($_POST['disconnect'] == 'disconnect') {
        $result = json_decode($response);
        if ($result->status == 'SUCCESS') {
            update_option('en_fdo_company_id_status', 0);
        }
    }
    echo $response;
    exit();
}
add_action('rest_api_init', 'en_rest_api_init_status_saia');
function en_rest_api_init_status_saia()
{
    register_rest_route('fdo-company-id', '/update-status', array(
        'methods' => 'POST',
        'callback' => 'en_saia_fdo_data_status',
        'permission_callback' => '__return_true'
    ));
}

/**
 * Update FDO coupon data
 * @param array $request
 * @return array|void
 */
function en_saia_fdo_data_status(WP_REST_Request $request)
{
    $status_data = $request->get_body();
    $status_data_decoded = json_decode($status_data);
    if (isset($status_data_decoded->connection_status)) {
        update_option('en_fdo_company_id_status', $status_data_decoded->connection_status);
        update_option('en_fdo_company_id', $status_data_decoded->fdo_company_id);
    }
    return true;
}

add_filter('en_suppress_parcel_rates_hook', 'supress_parcel_rates');
if (!function_exists('supress_parcel_rates')) {
    function supress_parcel_rates() {
        $exceedWeight = get_option('en_plugins_return_LTL_quotes') == 'yes';
        $supress_parcel_rates = get_option('en_suppress_parcel_rates') == 'suppress_parcel_rates';
        return ($exceedWeight && $supress_parcel_rates);
    }
}
