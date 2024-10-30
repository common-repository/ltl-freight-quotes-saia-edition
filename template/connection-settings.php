<?php
/**
 * SAIA WooComerce | SAIA Test connection HTML Form
 * @package     Woocommerce SAIA Edition
 * @author      <https://eniture.com/>
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SAIA WooComerce | SAIA Test connection HTML Form
 */
class SAIA_Connection_Settings
{
    /**
     * SAIA Test connection Settings
     * @return array
     */
    public function saia_con_setting()
    {
        echo '<div class="connection_section_class_saia">';
        $settings = array(
            'section_title_saia' => array(
                'name' => __('', 'woocommerce_saia_quote'),
                'type' => 'title',
                'desc' => '<br> ',
                'id' => 'wc_settings_saia_title_section_connection',
            ),

            'accountnbr_saia' => array(
                'name' => __('Account Number ', 'woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => __('', 'woocommerce_saia_quote'),
                'id' => 'wc_settings_saia_accountnbr'
            ),
            'accountnbr_postal_code_saia' => array(
                'name' => __('Account Number Postal Code ', 'woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => __('', 'woocommerce_saia_quote'),
                'id' => 'wc_settings_saia_accountnbr_postal_code',

            ),
            'accountnbr_third_party_saia' => array(
                'name' => __('Third Party Account Number ', 'woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => __('', 'woocommerce_saia_quote'),
                'id' => 'wc_settings_saia_accountnbr_third_party',

            ),


            'userid_saia' => array(
                'name' => __('Username ', 'woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => __('', 'woocommerce_saia_quote'),
                'id' => 'wc_settings_saia_userid'
            ),

            'password_saia' => array(
                'name' => __('Password ', 'woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => __('', 'woocommerce_saia_quote'),
                'id' => 'wc_settings_saia_password'
            ),

            'plugin_licence_key_saia' => array(
                'name' => __('Eniture API Key ', 'woocommerce_saia_quote'),
                'type' => 'text',
                'desc' => __('Obtain a Eniture API Key Key from <a href="https://eniture.com/woocommerce-saia-ltl-freight/" target="_blank" >eniture.com </a>', 'woocommerce_saia_quote'),
                'id' => 'wc_settings_saia_plugin_licence_key'
            ),

            'section_end_saia' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_saia_plugin_licence_key'
            ),
        );
        return $settings;
    }
}
