Nitrapi-PHP
===========

[![Latest Stable Version](https://poser.pugx.org/nitrado/nitrapi-php-lib/v/stable.png)](https://packagist.org/packages/nitrado/nitrapi-php-lib)
[![Latest Unstable Version](https://poser.pugx.org/nitrado/nitrapi-php-lib/v/unstable.svg)](https://packagist.org/packages/nitrado/nitrapi-php-lib)
[![Total Downloads](https://poser.pugx.org/nitrado/nitrapi-php-lib/downloads.png)](https://packagist.org/packages/nitrado/nitrapi-php-lib)

Official PHP based SDK for the Nitrapi RESTful API.


Recommends
---------

* PHP 5.5 or higher
* Composer


Installation
------------

Edit the composer.json and execute composer.phar update
``` php
{
    "require": {
        "nitrado/nitrapi-php-lib": "dev-master",
    }
}
```

Example
-------

```php
<?php

require_once '../vendor/autoload.php';

try {
    $api = new \Nitrapi\Nitrapi("<accesss token>");
    
    var_dump($api->getServices());
    
} catch(\Exception $e) {
    var_dump("API Error: " . $e->getMessage());
}
```

Access Tokens
-------
1. Go to **www.nitrado.com** then click **Log In**.
2. **Login** to your Nitrado Account.
3. Once you are logged in, click **the user menu at the top right**, then click **My Account**.
4. From the **My Account screen**, under the **options section** click **Developer Portal**.
5. From the Developer Portal, click **Long-life tokens**.
6. **Authenticate** with your nitrado account password, then click **Verify**.
7. **Type in an identification description** for the token, then **select which permission scopes you require** from the checkboxes below, then click **Create**.
8. You will see a message at the top saying 
`A new long-life token has been created, please note this token:` with the token code after. **Copy this token and save it**. 
***!!! NOTE: Never share this with anybody else !!!***
