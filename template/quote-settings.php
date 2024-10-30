<?php
/**
 * SAIA WooComerce | Qoute Settings Page
 * @package     Woocommerce SAIA Edition
 * @author      <https://eniture.com/>
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_footer', 'saia_quote_btn_text');
/**
 * SAIA JQuery Scripts for Giving name to the button
 */
function saia_quote_btn_text()
{
    echo '<script>
            jQuery( document ).ready(function() {
                jQuery(".quote_section_class_saia .button-primary, .quote_section_class_saia .is-primary").val("Save Changes");
                jQuery(".connection_section_class_saia .button-primary, .connection_section_class_saia .is-primary").val("Save Changes");
            });
        </script>';
}

/**
 * SAIA WooComerce | Qoute Settings Page
 */
class SAIA_Quote_Settings
{
    /**
     * Quote Setting From Fields
     * @return array
     */
    function saia_quote_settings_tab()
    {
        $disable_hold_at_terminal = "";
        $hold_at_terminal_package_required = "";

        $action_hold_at_terminal = apply_filters('saia_quotes_quotes_plans_suscription_and_features', 'hold_at_terminal');
        if (is_array($action_hold_at_terminal)) {
            $disable_hold_at_terminal = "disabled_me";
            $hold_at_terminal_package_required = apply_filters('saia_quotes_plans_notification_link', $action_hold_at_terminal);
        }

        $ltl_enable = get_option('en_plugins_return_LTL_quotes');
        $weight_threshold_class = $ltl_enable == 'yes' ? 'show_en_weight_threshold_lfq' : 'hide_en_weight_threshold_lfq';
        $weight_threshold = get_option('en_weight_threshold_lfq');
        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;


        echo '<div class="quote_section_class_saia">';
        $settings = array(
            'section_title_quote' => array(
                'title' => __('Quote Settings ', 'woocommerce_saia_quote'),
                'type' => 'title',
                'desc' => '',
                'id' => 'saia_section_title_quote'
            ),

            'label_as_saia' => array(
                'name' => __('Label As ', 'woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => '<span class="desc_text_style"> What the user sees during checkout, e.g. "LTL Freight". If left blank, "Freight" will display as the shipping method.</span>',
                'id' => 'saia_label_as'
            ),

            'price_sort_saia' => array(
                'name' => __("Don't sort shipping methods by price  ", 'woocommerce-settings-saia-quotes'),
                'type' => 'checkbox',
                'desc' => 'By default, the plugin will sort all shipping methods by price in ascending order.',
                'id' => 'shipping_methods_do_not_sort_by_price'
            ),

            'saia_show_delivery_estimate' => array(
                'name' => __('Show Delivery Estimate ', 'woocommerce_saia_quote'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce_saia_quote'),
                'id' => 'saia_delivey_estimate',
                'class' => 'saiaCheckboxClass'
            ),

            'accessorial_quoted_saia' => array(
                'title' => __('', 'woocommerce'),
                'name' => __('', 'woocommerce_saia_quote'),
                'desc' => '',
                'id' => 'woocommerce_accessorial_quoted_saia',
                'css' => '',
                'default' => '',
                'type' => 'title',
            ),

            'accessorial_quoted_saia' => array(
                'title' => __('', 'woocommerce'),
                'name' => __('', 'woocommerce_saia_quote'),
                'desc' => '',
                'id' => 'woocommerce_saia_accessorial_quoted',
                'css' => '',
                'default' => '',
                'type' => 'title',
            ),

            'residential_delivery_options_label' => array(
                'name' => __('Residential Delivery', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'residential_delivery_options_label'
            ),

            'accessorial_residential_delivery_saia' => array(
                'name' => __('Always quote as residential delivery ', 'woocommerce_saia_quote'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce_saia_quote'),
                'id' => 'saia_residential',
                'class' => 'accessorial_service saiaCheckboxClass',
            ),

//          Auto-detect residential addresses notification
            'avaibility_auto_residential' => array(
                'name' => __('Auto-detect residential addresses', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                'id' => 'avaibility_auto_residential'
            ),

            'liftgate_delivery_options_label' => array(
                'name' => __('Lift Gate Delivery ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'liftgate_delivery_options_label'
            ),

            'accessorial_liftgate_delivery_saia' => array(
                'name' => __('Always quote lift gate delivery ', 'woocommerce_saia_quote'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce_saia_quote'),
                'id' => 'saia_liftgate',
                'class' => 'accessorial_service saiaCheckboxClass checkbox_fr_add',
            ),

            'saia_quotes_liftgate_delivery_as_option' => array(
                'name' => __('Offer lift gate delivery as an option ', 'woocommerce-settings-fedex_freight'),
                'type' => 'checkbox',
                'desc' => __('', 'woocommerce-settings-fedex_freight'),
                'id' => 'saia_quotes_liftgate_delivery_as_option',
                'class' => 'accessorial_service checkbox_fr_add saiaCheckboxClass',
            ),

//          Use my liftgate notification
            'avaibility_lift_gate' => array(
                'name' => __('Always include lift gate delivery when a residential address is detected', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                'id' => 'avaibility_lift_gate'
            ),

            // Start Hot At Terminal
            'saia_ltl_hold_at_terminal_checkbox_status' => array(
                'name' => __('Hold At Terminal', 'woocommerce-settings-woocommerce_saia_quote'),
                'type' => 'checkbox',
                'desc' => 'Offer Hold At Terminal as an option ' . $hold_at_terminal_package_required,
                'class' => $disable_hold_at_terminal,
                'id' => 'saia_ltl_hold_at_terminal_checkbox_status',
            ),
            'saia_ltl_hold_at_terminal_fee' => array(
                'name' => __('', 'ground-transit-settings-woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => 'Adjust the price of the Hold At Terminal option.Enter an amount, e.g. 3.75, or a percentage, e.g. 5%.  Leave blank to use the price returned by the carrier.',
                'class' => $disable_hold_at_terminal,
                'id' => 'saia_ltl_hold_at_terminal_fee'
            ),
            'saia_ltl_hold_at_terminal_remove_address' => array(
                'name' => __('', 'woocommerce-settings-woocommerce_saia_quote'),
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => 'Show terminal information for HAT option ',
                'class' => $disable_hold_at_terminal,
                'id' => 'saia_ltl_hold_at_terminal_remove_address',
            ),
            // Handling Unit
            'saia_freight_label_handling_unit' => array(
                'name' => __('Handling Unit ', 'saia_freight_freight_wc_settings'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'saia_freight_label_handling_unit'
            ),
            // Handling Weight
            'saia_freight_handling_weight' => array(
                'name' => __('Weight of Handling Unit  ', 'saia_freight_wc_settings'),
                'type' => 'text',
                'desc' => 'Enter in pounds the weight of your pallet, skid, crate or other type of handling unit you use. The amount entered will be added to shipment weight prior to requesting a quote.',
                'id' => 'saia_freight_settings_handling_weight'
            ),

            'saia_freight_freight_maximum_handling_weight' => array(
                'name' => __('Maximum Weight per Handling Unit  ', 'saia_freight_freight_wc_settings'),
                'type' => 'text',
                'desc' => 'Enter in pounds the maximum weight that can be placed on the handling unit.',
                'id' => 'saia_freight_freight_maximum_handling_weight'
            ),
            
            'handing_fee_markup_saia' => array(
                'name' => __('Handling Fee / Markup ', 'woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => '<span class="desc_text_style">Amount excluding tax. Enter an amount, e.g 3.75, or a percentage, e.g, 5%. Leave blank to disable.</span>',
                'id' => 'saia_handling_fee'
            ),

            // Enale Logs
            'enale_logs_saia' => array(
                'name' => __("Enable Logs  ", 'woocommerce_odfl_quote'),
                'type' => 'checkbox',
                'desc' => 'When checked, the Logs page will contain up to 25 of the most recent transactions.',
                'id' => 'enale_logs_saia'
            ),

            'allow_other_plugins_saia' => array(
                'name' => __('Show WooCommerce Shipping Options ', 'woocommerce_saia_quote'),
                'type' => 'select',
                'default' => '3',
                'desc' => __('<span class="desc_text_style">Enabled options on WooCommerce Shipping page are included in quote results.</span>', 'woocommerce_saia_quote'),
                'id' => 'saia_allow_other_plugins',
                'options' => array(
                    'yes' => __('YES', 'YES'),
                    'no' => __('NO', 'NO'),
                )
            ),

            'return_SAIA_quotes' => array(
                'name' => __("Return LTL quotes when an order parcel shipment weight exceeds the weight threshold  ", 'woocommerce-settings-saia_quetes'),
                'type' => 'checkbox',
                'desc' => '<span class="desc_text_style">When checked, the LTL Freight Quote will return quotes when an order’s total weight exceeds the weight threshold (the maximum permitted by WWE and UPS), even if none of the products have settings to indicate that it will ship LTL Freight. To increase the accuracy of the returned quote(s), all products should have accurate weights and dimensions. </span>',
                'id' => 'en_plugins_return_LTL_quotes',
                'class' => 'saiaCheckboxClass'
            ),
            // Weight threshold for LTL freight
            'en_weight_threshold_lfq' => [
                'name' => __('Weight threshold for LTL Freight Quotes  ', 'woocommerce-settings-saia_quetes'),
                'type' => 'text',
                'default' => $weight_threshold,
                'class' => $weight_threshold_class,
                'id' => 'en_weight_threshold_lfq'
            ],
            'en_suppress_parcel_rates' => array(
                'name' => __("", 'woocommerce-settings-saia_quetes'),
                'type' => 'radio',
                'default' => 'display_parcel_rates',
                'options' => array(
                    'display_parcel_rates' => __("Continue to display parcel rates when the weight threshold is met.", 'woocommerce'),
                    'suppress_parcel_rates' => __("Suppress parcel rates when the weight threshold is met.", 'woocommerce'),
                ),
                'class' => 'en_suppress_parcel_rates',
                'id' => 'en_suppress_parcel_rates',
            ),
            'unable_retrieve_shipping_clear_abf' => array(
                'title' => __('', 'woocommerce'),
                'name' => __('', 'woocommerce-settings-abf-quotes'),
                'desc' => '',
                'id' => 'unable_retrieve_shipping_clear_abf',
                'css' => '',
                'default' => '',
                'type' => 'title',
            ),
            'unable_retrieve_shipping_abf' => array(
                'name' => __('Checkout options if the plugin fails to return a rate ', 'woocommerce-settings-abf_quetes'),
                'type' => 'title',
                'desc' => '<span>When the plugin is unable to retrieve shipping quotes and no other shipping options are provided by an alternative source:</span>',
                'id' => 'wc_settings_unable_retrieve_shipping_abf',
            ),

            'pervent_checkout_proceed_abf' => array(
                'name' => __('', 'woocommerce-settings-abf_quetes'),
                'type' => 'radio',
                'id' => 'pervent_checkout_proceed_abf_packages',
                'options' => array(
                    'allow' => __('', 'woocommerce'),
                    'prevent' => __('', 'woocommerce'),
                ),
                'id' => 'wc_pervent_proceed_checkout_eniture',
            ),

            'section_end_quote' => array(
                'type' => 'sectionend',
                'id' => 'saia_quote_section_end'
            )
        );
        return $settings;
    }
}
