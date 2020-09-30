<?php
/** 
 * Update cloudflare zone content for ddns.
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

	define('REQUEST_TIME', $_SERVER["REQUEST_TIME"]??time());
	$config = __DIR__.'/CloudflareApiConfig.php';
	if(file_exists($config)) {
		require_once $config;
	} else {
		// echo "please setup src/config.php from src/config-sample.php\n";
	}
	require_once __DIR__.'/RegistrarApi.php';
	require_once __DIR__.'/CloudflareApi.php';

	// config
	$dryrun				??= TRUE;							// 
	$url_check_ip		??= "https://ipv4.myip.info/";		// 
	$domain				??= "example.com";					// 
	$check_previous_ip	??= TRUE;							// TRUE: Only rewrite records with the previous IP address
	$target_record_type	??= ['type'=>'A'];					// 
	$target_record_name	??= [$domain];						// FALSE: rewrite all records


	// 
	try {
		$api = "\\orehid\\registrarapi\\CloudflareApi";

		// check domain
		$domain = $api::filterDomain ( $domain );
		$file_domain_id			= __DIR__."/".strtolower($api::NAME).".{$domain}.id";		// cache of cloudflare zone id
		$file_domain_ipaddress	= __DIR__."/".strtolower($api::NAME).".{$domain}.ip";		// cache of ip address

		// get zone id
		if(!file_exists($file_domain_id)) {
			echo "get zone id\n";
			$results = $api::listZones ($domain);
			$api::isError($results);
			$zone_id = $results->result[0]->id;
			file_put_contents ($file_domain_id, $results->result[0]->id);
		} else {
			$zone_id = file_get_contents ($file_domain_id);
		}

		// get current ip address and check change
		$ipaddress_previous = FALSE;
		$ipaddress_changed = FALSE;
		if(file_exists($file_domain_ipaddress)) {
			$ipaddress_previous = trim(file_get_contents ( $file_domain_ipaddress ));
		}
        $api::log('---- get IP address ----');
		$ipaddress_current = $api::getIpAddress($url_check_ip);
		if (filter_var($ipaddress_current, FILTER_VALIDATE_IP)) {
			if( $ipaddress_previous !== $ipaddress_current ) {
				$api::log("new ip address - ".$ipaddress_current);
				file_put_contents ( $file_domain_ipaddress, $ipaddress_current );
				$ipaddress_changed = TRUE;
			} else {
				$api::log("skip : ip address has not been changed - ".$ipaddress_current);
			}
		} else {
			$api::log("error : invalid ip address - ".$ipaddress_current);
		}

		if( $ipaddress_changed ) {

			// get dns records
			$results = $api::listDnsRecords ($zone_id, $target_record_type);
			$api::isError($results);
			$results = $results->result;

			// update dns records
			foreach($results as $result) {

				if($target_record_name && !in_array($result->name, $target_record_name, TRUE)) {
					// echo "skip : ".$result->name." is not in target name list.\n";
					continue;
				}
				if($check_previous_ip && $ipaddress_previous && ($result->content!==$ipaddress_previous)) {
					echo "skip : current record ({$result->content}) is not match a previous IP address ({$ipaddress_previous}).\n";
					continue;
				}
				if($result->content===$ipaddress_current) {
					echo "skip : current record is already the new value {$ipaddress_current}.\n";
					continue;
				}

				$api::log("update : '{$result->name}' : {$result->content} -> {$ipaddress_current}");
				if(!$dryrun) {
					$result->content = $ipaddress_current;
					$api::isError($api::updateDnsRecords ( $result ));
				} else {
					$api::log("skip : cause DRYRUN setting");
				}
			}

		}

	} catch (\Exception $e) {
	    echo PHP_EOL."*** ".$e->getMessage()." ***".PHP_EOL;
	}

	exit;

