<?php
// https://www.value-domain.com/vdapi/
define( 'VALUEDOMAIN_APIKEY','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

// ds record
$ds_records = [
    "keytag"      => "9999",
    "alg"         => "13",   // 13: ECDSAP256SHA256
    "digesttype"  => "2",    //  2:  SHA256
    "digest"      => "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
];

// $domain_id   = '999999'; // If you know the value, write it down
$domain = 'example.com';

// logging
//define('VALUEDOMAIN_LOG_FILES',[
//    __DIR__.'/valuedomain.log',
//    'php://stdout',
//]);
//define('CLOUDFLARE_LOG_STYLE', 2);  // 1:verbose 2:simple
