<?php
/**
 * WWE LTL Distance Get
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Get_saia_quotes_distance
 */
class Get_saia_quotes_distance
{
    /**
     * Get Distance Function
     * @param $map_address
     * @param $accessLevel
     * @return json
     */
    function saia_quotes_get_distance($map_address, $accessLevel, $destinationZip = array())
    {

        $domain = saia_quotes_get_domain();
        $post = array(
            'acessLevel' => $accessLevel,
            'address' => $map_address,
            'originAddresses' => (isset($map_address)) ? $map_address : "",
            'destinationAddress' => (isset($destinationZip)) ? $destinationZip : "",
            'eniureLicenceKey' => get_option('wc_settings_saia_plugin_licence_key'),
            'ServerName' => $domain,
        );

        if (is_array($post) && count($post) > 0) {
            $ltl_curl_obj = new SAIA_Curl_Request();
            $output = $ltl_curl_obj->saia_get_curl_response(SAIA_HITTING_DOMAIN_URL . '/addon/google-location.php', $post);
            return $output;
        }
    }
}
