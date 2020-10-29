<?php
/** 
 * Valuedomain api wrapper for dnssec setting
 *
 * Bearer auth requests an api token.
 *
 * PHP version 7
 *
 * @category Library
 * @package  registrarapi
 * @author   orehid <orehid@example.com>
 * @license  MIT
 * @version  1.0
 * @link     https://www.value-domain.com/api/doc/domain/
 */


namespace orehid\registrarapi;

/**
 *   ValuedomainApi
 */

class ValuedomainApi extends RegistrarApi
{
    /**
     *  constants
     */
    public const NAME           = 'VALUEDOMAIN';
    protected const ENDPOINT    = "https://api.value-domain.com/v1";
    protected const EOL         = "\n";

    /**
     *  settings
     */
    protected const APIKEY       = 'YOUR_API_KEY';
    protected const LOG_FILES    = [  // empty: no logging
        __DIR__.'/valuedomain.log',
        'php://stdout',               // 'php://stdout' for screen
    ];


    /**
     * api calls
     */

    /**
     * @link https://www.value-domain.com/api/doc/domain/#tag/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3
     */
    public static function getDomains()
    {
        self::log('---- get Domains ----');
        $result = self::request('/domains');
        return $result;
    }

    /**
     * @link https://www.value-domain.com/api/doc/domain/#tag/DNSSEC
     */
    public static function getDomainsDnssec(string $domain="")
    {
        if($domain) {
            self::log('---- get '.$domain.' DNSSEC info ----');
            $result = self::request("/domains/{$domain}/dnssec");
        } else {
            self::log('---- get all Domains DNSSEC info ----');
            $result = self::request('/domains/dnssec');
        }

        return $result;
    }

    /**
     * @link https://www.value-domain.com/api/doc/domain/#tag/DNSSEC
     */
    public static function setDomainsDnssec(string $domain, array $payload)
    {
        self::log('---- set Domains Dnssec ----');
        $result = self::request("/domains/{$domain}/dnssec", 'PUT', $payload);
        return $result;
    }


    /**
     *    helper functions
     */

    /**
     * isError
     */
    public static function isError(object $response) : void
    {
        if (isset($response->errors)) {
            self::log('---- api error ----');
            $errors = (array) ($response->errors??[]);
            foreach ($errors as $k=>$v) {
                self::log($k.":".$v);
            }
            throw new \Exception('api returns errors');
        }
    }


    public static function request( string $path, string $method="GET", array $posts=[])
    {
        $apikey = (defined('self::APIKEY') ? self::APIKEY : false);
        $apikey = (defined('VALUEDOMAIN_APIKEY') ? VALUEDOMAIN_APIKEY : $apikey);
        if(!$apikey) {
            self::log('Error: no apikey setting');
            return false;
        }

        $post_query = json_encode($posts);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, static::ENDPOINT . $path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($method!=='GET') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_query);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $headers = [];
        $headers[] = "Content-Type: application/json";
        if ($method!=='GET') {
            $headers[] = 'Content-Length: ' . strlen($post_query);
        }
        $headers[] = 'Authorization: Bearer '. $apikey;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            self::log('Curl Error: '. curl_errno($ch). ' - '. curl_error($ch));
        } else {
            $response = json_decode($response);
        }
        // self::log(curl_getinfo($ch));
        curl_close($ch);
        self::log($response);
        return $response;
    }


}
