<?php
/**
 * SAIA WooComerce | Test connection AJAX Request
 * @package     Woocommerce SAIA Edition
 * @author      <https://eniture.com/>
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_nopriv_saia_action', 'saia_test_submit');
add_action('wp_ajax_saia_action', 'saia_test_submit');
/**
 * Test connection AJAX Request
 */
function saia_test_submit()
{
    $domain = saia_quotes_get_domain();

    $saia_accountnbr = '';
    $saia_thirdparty_accountnbr = sanitize_text_field($_POST['saia_thirdparty_accountnbr']);

    if ($saia_thirdparty_accountnbr != '') {
        $saia_accountnbr = $saia_thirdparty_accountnbr;
    } else {
        $saia_accountnbr = sanitize_text_field($_POST['saia_accountnbr']);
    }
    $data = array(
        'licenseKey' => (isset($_POST['saia_plugin_license'])) ? sanitize_text_field($_POST['saia_plugin_license']) : "",
        'serverName' => $domain,
        'carrierName' => 'saia',
        'plateform' => 'WordPress',
        'carrier_mode' => 'test',
        'userID' => (isset($_POST['saia_userid'])) ? sanitize_text_field($_POST['saia_userid']) : "",
        'password' => (isset($_POST['saia_password'])) ? sanitize_text_field($_POST['saia_password']) : "",
        'accountNumber' => $saia_accountnbr
    );

    $saia_curl_obj = new SAIA_Curl_Request();
    $sResponseData = $saia_curl_obj->saia_get_curl_response(SAIA_HITTING_DOMAIN_URL . '/index.php', $data);
    $sResponseData = json_decode($sResponseData);

    if (isset($sResponseData->severity) && $sResponseData->severity == 'SUCCESS') {
        $sResult = array('message' => "success");
    } elseif (isset($sResponseData->severity) && $sResponseData->severity == 'ERROR') {
        $sResult = $sResponseData->message;
        $fullstop = (substr($sResult, -1) == '.') ? '' : '.';
        $sResult = array('message' => $sResult . $fullstop);
    } else {
        $sResult = array('message' => "failure");
    }

    echo json_encode($sResult);
    exit();
}
