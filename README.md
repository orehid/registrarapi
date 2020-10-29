# registrarapi
***This project is my sandbox for learning github.***

* simple API wrapper.  
 This project support APIs for DDNS or DNSSEC setteing only.  
 [Cloudflare API](https://api.cloudflare.com/ "Cloudflare API")  
 [value-domain API](https://www.value-domain.com/api/doc/domain/ "バリュードメインAPI")  

* All API responses can be logged.

* PHP7 or higher is required.
* curl.cainfo setting in php.ini required.
* No other libraries are required.
* Composer is not necessarily required.
* Developed on Windows. It hasn't been tested on any other OS.

## Install
* composer require orehid/registrarapi

___

### What CloudflareApiUpdateDdns.php does
* Get the current global IP address.
* If it has been changed, update the target DNS records.

### CloudflareApiUpdateDdns.php usage
* Setup the Cloudflont Account.
* Get a API TOKEN with zone edit permission.
* Copy src/CloudflareApiConfig.sample.php to src/CloudflareApiConfig.php
* Edit src/CloudflareApiConfig.php to fit your environment.

* Execute or cron, php CloudflareApiUpdateDdns.php

___

### What ValuedomainApiSetDnssec.php does
* Get current DNSSEC settings.
* Update DNSSEC settings.

### ValuedomainApiSetDnssec.php usage
***NOTICE: The program does not work with the error "Digest is invalid".***
* Setup the Cloudflont Account.
* Get a API KEY.
* Get a DNSSEC infomation.
* Copy src/ValuedomainApiConfig.sample.php to src/ValuedomainApiConfig.php
* Edit src/ValuedomainApiConfig.php to fit your environment.

* Execute php ValuedomainApiSetDnssec.php

___


# License
The source code is licensed MIT.


