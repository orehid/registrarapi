<?php
/** 
 * Cloudflare API wrapper to update ipaddress for ddns
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
 * @link     https://api.cloudflare.com/
 */


namespace orehid\registrarapi;

/**
 *   CloudflareApi
 */

class CloudflareApi extends RegistrarApi
{
    /**
     *  settings
     */

    protected const API_TOKEN = 'YOUR_API_TOKEN';   // or global const CLOUDFLARE_API_TOKEN
    protected const LOG_FILES = [                   // empty: no logging
        __DIR__.'/cloudflare.log',
        'php://stdout',                             // 'php://stdout' for screen
    ];
	protected const TARGET_RECORD_TYPE = ['type'=>'A'];

    /**
     *  constants
     */

    public const NAME = 'CLOUDFLARE';
    protected const ENDPOINT = "https://api.cloudflare.com/client/v4";
    protected const EOL = "\n";

    /**
     * api calls
     */

    /**
     * @link https://api.cloudflare.com/#zone-list-zones
     */
    public static function listZones( string $domain="" )
    {
        self::log('---- List Zones ----');
        $params = [];
        if ($domain) {
            $params['name'] = $domain;
        }
        $result = self::request('/zones', 'GET', $params);
        return $result;
    }

    /**
     * @link https://api.cloudflare.com/#dns-records-for-a-zone-list-dns-records
     */
    public static function listDnsRecords( string $zone_id, array $params=[] )
    {
        self::log('---- List Dns Records ----');
        $result = self::request("/zones/{$zone_id}/dns_records", 'GET', $params);
        return $result;
    }

    /**
     * https://api.cloudflare.com/#dns-records-for-a-zone-list-dns-records
     *
     * @record must be one of results by ListDnsRecords
     */
    public static function updateDnsRecords( $record )
    {
        self::log('---- update Dns Records ----');
        $params = [
            'type' => $record->type,                // "A"
            'name' => $record->name,                // "www.example.com"
            'content'=> $record->content,           // (ip address)
            'ttl'=> $record->ttl,                   // 1:auto
        ];
        $result = self::request("/zones/{$record->zone_id}/dns_records/{$record->id}", 'GET', $params);
        return $result;
    }


    /**
     * helper functions
     */

    /**
     * getIpAddress
     */
    public static function getIpAddress( string $url ) : string
    {
		$result = file_get_contents($url);
		foreach ($http_response_header as $h) {
		    switch(strtolower($h)) {
				case 'content-type: application/json':
					$arr = json_decode($result);
					foreach ($arr as $v) {
						if(filter_var($v, FILTER_VALIDATE_IP)) {
							$result = $v;
							break 3;
						}
					}
					$result = (string) $t;
					break 2;
			}
		}
		if (!filter_var($result, FILTER_VALIDATE_IP)) {
            throw new \Exception('wrong domain name - '.$result);
		}
        return $result;
    }

    /**
     * isError
     */
    public static function isError(object $response) : void
    {
        $f = $response->success??false;
        if (!$f) {
            self::log('---- api error ----');
            $errors = (array) ($response->errors??[]);
            foreach ($errors as $k=>$v) {
                self::log($k.":".$v->code.":".$v->message);
            }
            throw new \Exception('api returns errors');
        }
    }

    /**
     * request
     */
    public static function request( string $path="/", string $method="GET", array $params=[], array $headers_extra=[]) : object
    {
        $api_token = self::getEnv('API_TOKEN');
        if (!$api_token || $api_token==='YOUR_API_TOKEN') {
            $message = 'error : wrong api token - '.$api_token;
            self::log($message);
            throw new \Exception($message);
        }

        $headers = (
        [
        'Authorization: Bearer '.$api_token,
        'Content-Type: application/json',
        ] + $headers_extra
        );

        if ($method ==='GET' && $params ) {
            $path .= '?'.http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_URL, static::ENDPOINT . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        if ($method!=='GET') {
            $params = json_encode($params);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $headers[] = 'Content-Length: ' . strlen($params);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $message = 'error : curl - '. curl_errno($ch). ' - '. curl_error($ch);
            self::log($message);
            throw new \Exception($message);
        }
        curl_close($ch);
        $response = json_decode($response);
        // self::log(curl_getinfo($ch));
        self::log($response);

        return $response;
    }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */