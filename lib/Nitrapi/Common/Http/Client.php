<?php

namespace Nitrapi\Common\Http;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Curl\CurlVersion;
use Nitrapi\Common\Exceptions\NitrapiException;

class Client extends GuzzleClient
{
    const VERSION = '1.0.0';
    const MINIMUM_PHP_VERSION = '5.4.0';

    public function __construct($baseUrl = '', $config = null) {
        if (PHP_VERSION < self::MINIMUM_PHP_VERSION) {
            throw new NitrapiException(sprintf(
                'You must have PHP version >= %s installed.',
                self::MINIMUM_PHP_VERSION
            ));
        }

        parent::__construct($baseUrl, $config);
    }

    public function getDefaultUserAgent() {
        return 'Nitrapi/' . self::VERSION
        . ' cURL/' . CurlVersion::getInstance()->get('version')
        . ' PHP/' . PHP_VERSION;
    }

    public function getUserAgent() {
        return $this->userAgent;
    }
}