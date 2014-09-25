<?php

namespace Nitrapi;

use Nitrapi\Common\Http\Client;
use Nitrapi\Services\Service;
use Nitrapi\Services\ServiceCollection;
use Nitrapi\Services\ServiceFactory;

define('NITRAPI_LIVE_URL', 'https://api.nitrado.net/');
define('NITRAPI_DEV_URL', 'http://0.0.0.0:3000/');

class Nitrapi extends Client
{
    protected $accessToken;

    public function __construct($accessToken, $options = []) {
        $this->setAccessToken($accessToken);

        parent::__construct(NITRAPI_DEV_URL, $options);

        $this->setDefaultOption('query', [
            'access_token' => $this->getAccessToken()
        ]);
    }

    /**
     * Gets a specific service
     *
     * @param array $options
     * @return Service
     */
    public function getService(array $options = []) {
        return ServiceFactory::factory($this, $options);
    }

    /**
     * @param array $options
     * @return array
     */
    public function getServices(array $options = []) {
        return (new ServiceCollection($this, $options))->getServices();
    }

    protected function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;

        return $this;
    }

    protected function getAccessToken() {
        return $this->accessToken;
    }
}