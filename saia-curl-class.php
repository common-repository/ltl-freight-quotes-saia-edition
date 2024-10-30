<?php
/**
 * SAIA WooComerce | Get Curl Response Class
 * @package     Woocommerce SAIA Edition
 * @author      <https://eniture.com/>
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SAIA WooComerce | Get Curl Response Class
 */
class SAIA_Curl_Request
{
    /**
     * Get Curl Response
     * @param $url
     * @param $postData
     * @return Response JSON
     */
    function saia_get_curl_response($url, $postData)
    {
        if (!empty($url) && !empty($postData)) {
            $field_string = http_build_query($postData);

            $response = wp_remote_post($url,
                array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $field_string,
                )
            );

            $output = wp_remote_retrieve_body($response);

            return $output;
        }
    }
}