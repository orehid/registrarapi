# registrarapi
***This project is my sandbox for learning github.***

* simple API wrapper.
This project support APIs for DDNS or DNSSEC setteing only.
* All API responses can be logged.

* PHP7 or higher is required.
* No other libraries are required.
* Developed on Windows. It hasn't been tested on any other OS.

## Install
* composer orehid/registrarapi

___

### What CloudflareApiUpdateDdns.php does
* Get the current global IP address.
* If it has been changed, update the target DNS records.

### CloudflareApiUpdateDdns.php usage
* Setup the Cloudflont Account.
* Get a API TOKEN with zone edit permission.
* Copy src/CloudflareApiConfig.sample.php to src/CloudflareApiConfig.sample
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
* Copy src/ValuedomainApiConfig.sample.php to src/ValuedomainApiConfig.sample
* Edit src/ValuedomainApiConfig.php to fit your environment.

* Execute php ValuedomainApiSetDnssec.php

___


# License
The source code is licensed MIT.


