Nitrapi-PHP
===========

[![Latest Stable Version](https://poser.pugx.org/nitrado/nitrapi-php-lib/v/stable.png)](https://packagist.org/packages/nitrado/nitrapi-php-lib)
[![Latest Unstable Version](https://poser.pugx.org/nitrado/nitrapi-php-lib/v/unstable.svg)](https://packagist.org/packages/nitrado/nitrapi-php-lib)
[![Total Downloads](https://poser.pugx.org/nitrado/nitrapi-php-lib/downloads.png)](https://packagist.org/packages/nitrado/nitrapi-php-lib)

Official PHP-based SDK for the Nitrapi RESTful API.


Requirements
------------

* PHP 7.3 or higher
* Composer
* A [PSR-18](https://www.php-fig.org/psr/psr-18/) HTTP client (e.g. `php-http/guzzle7-adapter`, `symfony/http-client`, `kriswallsmith/buzz`, …)

The library itself only depends on the PSR-7/17/18 interfaces.  
[`php-http/discovery`](https://github.com/php-http/discovery) is used to **auto-detect** an installed PSR-18 client at runtime, so you do not need to wire anything up if you already have a compatible client in your project.


Installation
------------

Add the library to your `composer.json`:

```json
{
    "require": {
        "nitrado/nitrapi-php-lib": "dev-master"
    }
}
```

or (preferred):

```shell
$ composer require nitrado/nitrapi-php-lib
```

Then install a PSR-18 HTTP client if you don't already have one, for example:

```bash
composer require php-http/guzzle7-adapter
```

Finally run:

```bash
composer install
```


Usage
-----

```php
<?php

require_once 'vendor/autoload.php';

try {
    // A PSR-18 client is discovered automatically.
    $api = new \Nitrapi\Nitrapi('<access_token>');

    var_dump($api->getServices());

} catch (\Exception $e) {
    echo 'API Error: ' . $e->getMessage() . PHP_EOL;
}
```


### Providing a custom HTTP client

If you want full control over the HTTP client (timeouts, proxies, TLS settings, …),
inject it via the `$options` array:

```php
<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;

$factory = new Psr17Factory();
$client  = new Psr18Client();

$api = new \Nitrapi\Nitrapi('<access_token>', [
    'http_client'     => $client,
    'request_factory' => $factory,
    'stream_factory'  => $factory,
]);
```


### Rate limiting

```php
if ($api->hasRateLimit()) {
    echo 'Requests remaining: ' . $api->getRemainingRequests() . PHP_EOL;
    echo 'Resets at: '         . $api->getRateLimitResetTime()->format('c') . PHP_EOL;
}
```

