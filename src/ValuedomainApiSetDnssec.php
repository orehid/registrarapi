<?php
/**
 * Valuedomain api wrapper for dnssec setting
 * 
 * PHP version 7
 *
 * @category Library
 * @package  registrarapi
 * @author   orehid <orehid@example.com>
 * @license  MIT
 * @version  1.0
 * @link     https://www.value-domain.com/api/doc/
 */

    define('REQUEST_TIME', $_SERVER["REQUEST_TIME"]??time());
    $config = __DIR__.'/ValuedomainApiConfig.php';
    if (file_exists($config)) {
        include_once $config;
    } else {
        // echo "please setup src/ValuedomainApiConfig.php\n";
    }
    require_once __DIR__.'/RegistrarApi.php';
    require_once __DIR__.'/ValuedomainApi.php';
    require_once __DIR__.'/ValuedomainApiConfig.php';

    // config
    $dryrun ??= true;

	// 
try {
    $api = "\\orehid\\registrarapi\\ValuedomainApi";

    $api::log('==== start ====');

	// check domain
	$domain = $api::filterDomain( $domain );
	$file_domain_id	= __DIR__."/".strtolower($api::NAME).".{$domain}.id";	// cache of domain id

/*
	// get domain id
	if(!isset($domain_id)) {
		if(!file_exists($file_domain_id)) {
			$domain_id = FALSE;
		    $result = $api::getDomains();
		    $api::isError($result);
		    foreach($result->results as $r) {
				if($r->domainname === $domain) {
					$domain_id = $r->domainid;
					break;
				}
			}
			if($domain_id) {
				file_put_contents ($file_domain_id, $domain_id);
			} else {
				echo "not found domain in domain list - {$domain}\n";
				exit;
			}
		} else {
			$domain_id = file_get_contents ($file_domain_id);
		}
	}
*/

	// get DNSSEC info
    $result = $api::getDomainsDnssec($domain);
    $api::isError($result);

	// set DNSSEC info
    $result = $api::setDomainsDnssec($domain,["ds_records"=>$ds_records]);
    $api::isError($result);

	//
	$api::log('==== finish ====');

} catch (\Exception $e) {
    echo PHP_EOL."*** ".$e->getMessage()." ***".PHP_EOL;
}

    exit;


