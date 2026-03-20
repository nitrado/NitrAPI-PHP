<?php

namespace Nitrapi;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Nitrapi\Admin\Admin;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiServiceTypeNotFoundException;
use Nitrapi\Common\Http\Client;
use Nitrapi\Customer\AccessToken;
use Nitrapi\Customer\Customer;
use Nitrapi\Customer\Registration;
use Nitrapi\GameInfo\GameInfoInterface;
use Nitrapi\Services\Service;
use Nitrapi\Services\ServiceCollection;
use Nitrapi\Services\ServiceFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

define('NITRAPI_LIVE_URL', 'https://api.nitrado.net/');

class Nitrapi extends Client
{
    protected $oAuthClientId;
    protected $oAuthClientSecret;

    /**
     * @param string|null $accessToken Bearer token for API authentication
     * @param array{
     *     user_ip: string,
     *     user_ipv6: string,
     *     oAuthClientId: string,
     *     oAuthClientSecret: string,
     *     http_client: ClientInterface,
     *     request_factory: RequestFactoryInterface,
     *     stream_factory: StreamFactoryInterface,
     * } $options Client options
     * @param string $url Base URL, defaults to NITRAPI_LIVE_URL
     *
     * @throws NitrapiException
     */
    public function __construct($accessToken, $options = [], $url = NITRAPI_LIVE_URL)
    {
        $query = [];
        if (isset($options['user_ip']) && filter_var($options['user_ip'], FILTER_VALIDATE_IP)) {
            $query['user_ip'] = $options['user_ip'];
        }
        if (isset($options['user_ipv6']) && filter_var($options['user_ipv6'], FILTER_VALIDATE_IP)) {
            $query['user_ipv6'] = $options['user_ipv6'];
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

        $httpClient = $options['http_client'] ?? Psr18ClientDiscovery::find();
        $requestFactory = $options['request_factory'] ?? Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = $options['stream_factory'] ?? Psr17FactoryDiscovery::findStreamFactory();

        parent::__construct(
            $httpClient,
            $requestFactory,
            $streamFactory,
            $url ?? NITRAPI_LIVE_URL,
            ['query' => $query],
        );

        $this->setAccessToken($accessToken);
    }

    /**
     * Gets a specific service.
     * @throws NitrapiServiceTypeNotFoundException
     * @throws NitrapiException
     */
    public function getService(array $options = []): Service
    {
        return ServiceFactory::factory($this, $options);
    }

    /**
     * Returns all services.
     * @throws NitrapiException
     */
    public function getServices(array $options = []): array
    {
        return (new ServiceCollection($this, $options))->getServices();
    }

    /**
     * Returns the admin controller.
     */
    public function getAdmin(): Admin
    {
        return new Admin($this);
    }

    /**
     * Returns the customer data set.
     * @throws NitrapiException
     */
    public function getCustomer(): Customer
    {
        return new Customer($this);
    }

    public function getGameInfoInterface(): GameInfoInterface
    {
        return new GameInfoInterface($this);
    }

    /**
     * @see https://doc.nitrado.net/#api-Registration-Create
     * @throws NitrapiException
     */
    public function registerUser(
        $userName,
        $email,
        $password,
        $consentPrivacy,
        $consentAge,
        $consentTos,
        $recaptchaResponse = null,
        $currency = null,
        $language = null,
        $timezone = null,
        $consentNewsletter = false
    ): Registration {
        return new Registration(
            $this,
            $this->oAuthClientId,
            $this->oAuthClientSecret,
            $userName,
            $email,
            $password,
            $consentPrivacy,
            $consentAge,
            $consentTos,
            $recaptchaResponse,
            $currency,
            $language,
            $timezone,
            $consentNewsletter,
        );
    }

    /**
     * @throws NitrapiException
     */
    public function getRecaptchaSiteKey()
    {
        return Registration::getRecaptchaSiteKey($this);
    }

    /**
     * @throws NitrapiException
     */
    public function getAccessTokenInfo(): AccessToken
    {
        return new AccessToken(
            array_merge(
                $this->dataGet('/token')['token'],
                ['access_token' => $this->getAccessToken()],
            ),
        );
    }
}
