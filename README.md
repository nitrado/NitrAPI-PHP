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
