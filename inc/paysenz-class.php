<?php

namespace paysenz\payment\gateway;

/**
 * Description of Paysenz
 *
 * @author Unlockive
 * @Date 02-11-2019
 */
class Paysenz {

    //const BASE_URL = 'http://erp.mmd.gov.bd/backend/web';
    const BASE_URL      = '';

    /** this is success url, after payment success paysenz will redirect this link
     * THIS LINK IS REQUIRED
     * */
    const SUCCESS_URL   = '';

    /** this is fail url, after payment success paysenz will redirect this link
     * THIS LINK IS REQUIRED
     * */
    const FAIL_URL      = '';

    /** this is cancel url, after payment success paysenz will redirect this link
     * THIS LINK IS REQUIRED
     * */
    const CANCEL_URL    =  '';

    /** this is ipn url, after payment success paysenz will redirect this link
     * THIS LINK IS REQUIRED
     * THIS IS OPTIONAL , IF YOU USE IT YOU PAYMENT THEN IT'S REQUIRED
     * */
    const IPN_URL       = '';

    const GRANT_TYPE        = 'password';
    const CLIENT_ID         = ''; //Required - Data come from dashbaord settings
    const CLIENT_SECRET     = ''; // Required - Data come from dashbaord settings
    const CLIENT_USER_NAME  = ''; // Required - Data come from dashbaord settings
    const CLIENT_PASSWORD   = ''; // Required - Data come from dashbaord settings
    const CLIENT_SCOPE      = '*';
    const ORDER_PREFIX      = ''; // NMI1

    const PAYSENZ_URL       = 'https://gopaysenz.com/';


    /**
     * @desc Get `access_token` for HTTP authorization
     * `access_token` validity is 3 months. So get the `access_token` first time, then
     * save it to Database or Application cache, so that you do not have to call this
     * methd every time.
     *
     */
    public static function retrieveToken($paymentSOption) {

        if (isset($_SESSION['paysenz_access_token'])) {
            return $_SESSION['paysenz_access_token'];
        } else {
            try {

                $requestParams = [
                    'grant_type'    => self::GRANT_TYPE,
                    'client_id'     => $paymentSOption['CLIENT_ID'], //self::CLIENT_ID,
                    'client_secret' => $paymentSOption['CLIENT_SECRET'], //self::CLIENT_SECRET,
                    'username'      => $paymentSOption['CLIENT_USER_NAME'], //self::CLIENT_USER_NAME,
                    'password'      => $paymentSOption['CLIENT_PASSWORD'], //self::CLIENT_PASSWORD,
                    'scope'         => self::CLIENT_SCOPE,
                ];

                $payload    = json_encode($requestParams);

                $response   = wp_safe_remote_post( self::PAYSENZ_URL.'oauth/token', array( 'body' => $payload, 'headers' => array('content-type' => 'application/json', 'content-Length' => strlen($payload)) )) ;
                $content    = json_decode((string) $response['body'], true);

                $_SESSION['paysenz_access_token'] = $content['access_token'];
                return $content['access_token'];

            } catch (Exception $e) {
                return array('error' => true);
            }
        }
    }

    /// Success, Fail, Cancel URL
    public static function paysenz_url() {
        $url = [
            'callback_success_url'  => self::BASE_URL.self::SUCCESS_URL,
            'callback_fail_url'     => self::BASE_URL.self::FAIL_URL,
            'callback_cancel_url'   => self::BASE_URL.self::CANCEL_URL,
            'callback_ipn_url'      => self::BASE_URL.self::IPN_URL
        ];
        return $url;
    }


    public static function paysenz_payment_get_realURL($requestParams, $paymentSOption) {

        $post           = json_encode($requestParams);
        $authorization  = self::retrieveToken($paymentSOption);
        $response       = wp_safe_remote_post( self::PAYSENZ_URL.'api/v1.0/pay', array( 'body' => $post, 'headers' => array('content-type' => 'application/json', 'Authorization' => 'Bearer '.$authorization) ));

        return json_decode($response['body']);
    }

}
