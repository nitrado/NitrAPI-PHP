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

    public function __construct($accessToken, $options = array()) {
        $this->setAccessToken($accessToken);

        parent::__construct(NITRAPI_LIVE_URL, $options);

        $query = array();
        if (!empty($accessToken)) {
            $query['access_token'] = $accessToken;
        }
        if (isset($options['user_id']) && !empty($options['user_id'])) {
            $query['user_id'] = (int)$options['user_id'];
        }
        $this->setDefaultOption('query', $query);
    }

    /**
     * Gets a specific service
     *
     * @param array $options
     * @return Service
     */
    public function getService(array $options = array()) {
        return ServiceFactory::factory($this, $options);
    }

    /**
     * @param array $options
     * @return array
     */
    public function getServices(array $options = array()) {
        $collection = new ServiceCollection($this, $options);

        return $collection->getServices();
    }

    protected function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;

        return $this;
    }

    protected function getAccessToken() {
        return $this->accessToken;
    }
}