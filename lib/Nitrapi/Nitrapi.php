<?php

namespace Nitrapi;

use Nitrapi\Admin\Admin;
use Nitrapi\Common\Http\Client;
use Nitrapi\Customer\Customer;
use Nitrapi\Services\Service;
use Nitrapi\Services\ServiceCollection;
use Nitrapi\Services\ServiceFactory;

define('NITRAPI_LIVE_URL', 'https://api.nitrado.net/');

class Nitrapi extends Client
{
    protected $accessToken;
    protected $oAuthClientId;
    protected $oAuthClientSecret;

    public function __construct($accessToken, $options = array(), $url = NITRAPI_LIVE_URL) {
        $this->setAccessToken($accessToken);

        $query = array();
        if (!empty($accessToken)) {
            $query['access_token'] = $accessToken;
        }

        if (isset($options['user_ip']) && filter_var($options['user_ip'], FILTER_VALIDATE_IP)) {
            $query['user_ip'] = $options['user_ip'];
        }

        if (!empty($options['locale'])) {
            $query['locale'] = (string)$options['locale'];
        }

        if (!empty($options['oAuthClientId'])) {
            $this->oAuthClientId = $options['oAuthClientId'];
        }

        if (!empty($options['oAuthClientSecret'])) {
            $this->oAuthClientSecret = $options['oAuthClientSecret'];
        }

        $options['query'] = $query;
        parent::__construct($url, $options);
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

    /**
     * Returns the admin controller
     *
     * @return Admin
     */
    public function getAdmin() {
        return new Admin($this);
    }

    /**
     * Gets the customer data set
     *
     * @return Customer
     */
    public function getCustomer() {
        return new Customer($this);
    }

    public function getGameInfoInterface() {
        return new \Nitrapi\GameInfo\GameInfoInterface($this);
    }

    public function registerUser($userName, $email, $password, $recaptchaResponse = null, $currency = null, $language = null, $timezone = null) {
        return new \Nitrapi\Customer\Registration($this, $this->oAuthClientId, $this->oAuthClientSecret, $userName, $email, $password, $recaptchaResponse, $currency, $language, $timezone);
    }

    public function getRecaptchaSiteKey() {
        return \Nitrapi\Customer\Registration::getRecaptchaSiteKey($this);
    }

    public function getAccessTokenInfo() {
        return new \Nitrapi\Customer\AccessToken(
            array_merge(
                $this->dataGet('/token')['token'],
                ['access_token' => $this->getAccessToken()]
            ));
    }

    protected function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;

        return $this;
    }

    protected function getAccessToken() {
        return $this->accessToken;
    }
}