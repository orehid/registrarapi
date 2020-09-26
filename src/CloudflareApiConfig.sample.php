<?php
/** 
 * Cloudflare Api Config
 *
 * PHP version 7
 *
 * @category Library
 * @package  registrarapi
 * @author   orehid <orehid@example.com>
 * @license  MIT
 * @version  1.0
 */



// The following global constants override class constants.

// API TOKEN
define('CLOUDFLARE_API_TOKEN',	'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

// LOGGING
// 		empty: no logging.
// 		'php://stdout' is for screen output.
define('CLOUDFLARE_LOG_FILES',[
    __DIR__.'/api.log',
    'php://stdout',
]);


// The following vars override vars in CloudflareApiUpdateDdns.php.

// DEBUG MODE
// 		TRUE: no update records.
$dryrun       = TURE;

// taget domain name
$domain       = "example.com";

// Consider current record values.
// 		TRUE: Rewrite only if the record is the same as the previous IP address.
$check_previous_ip	= FALSE;

// Target DNS record type
$target_record_type	= ['type'=>'A'];

// Target DNS record name
// 		array: list of record names
// 		FALSE: rewrite all records
$target_record_name	= [
//		'mail.'.$domain,
//		'*.'.$domain,
		$domain,
];

// URL to get the global IP address
$url_check_ip = "https://ipv4.myip.info/";			// string
// $url_check_ip = "https://ifconfig.io/ip";		// string
// $url_check_ip = "https://ifconfig.me/ip";		// string
// $url_check_ip = "https://httpbin.org/ip";		// json


