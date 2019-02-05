<?php

namespace Nitrapi\Common\Http;

use DateTime;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Nitrapi\Common\Exceptions\NitrapiConcurrencyException;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Common\Exceptions\NitrapiMaintenanceException;
use Nitrapi\Common\Exceptions\NitrapiRateLimitException;

class Client extends GuzzleClient
{
    const MINIMUM_PHP_VERSION = '5.5.0';

    protected $defaultQuery = [];

    protected $accessToken = null;

    // Rate Limit metadata
    /** @var integer */
    protected $rateLimit;
    /** @var integer */
    protected $remainingRequests;
    /** @var DateTime */
    protected $rateLimitResetTime;

    protected $clientCertificate;
    protected $clientCertificateKey;

    public function __construct($baseUrl = '', $config = null) {
        if (PHP_VERSION < self::MINIMUM_PHP_VERSION) {
            throw new NitrapiException(sprintf(
                'You must have PHP version >= %s installed.',
                self::MINIMUM_PHP_VERSION
            ));
        }
        if (isset($config['query'])) {
            $this->defaultQuery = $config['query'];
        }
        $config['base_uri'] = $baseUrl;
        parent::__construct($config);
    }

    /**
     * Specifies the path to a client certificate and key in PEM format.
     *
     * @param $cert
     * @param $privateKey
     * @return $this
     */
    public function setClientCertificate($cert, $privateKey) {
        $this->clientCertificate = $cert;
        $this->clientCertificateKey = $privateKey;

        return $this;
    }

    /**
     * Set a new access token.
     *
     * @param $accessToken
     * @return $this
     */
    protected function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Returns the current access token.
     *
     * @return null
     */
    protected function getAccessToken() {
        return $this->accessToken;
    }

    public function fillOptions(&$options) {
        if (!empty($this->accessToken)) {
            $options['headers']['Authorization'] = 'Bearer ' . $this-> accessToken;
        }
        if (!empty($this->clientCertificate) && file_exists($this->clientCertificate)) {
            $options[\GuzzleHttp\RequestOptions::CERT] = $this->clientCertificate;
        }
        if (!empty($this->clientCertificateKey) && file_exists($this->clientCertificateKey)) {
            $options[\GuzzleHttp\RequestOptions::SSL_KEY] = $this->clientCertificateKey;
        }
    }

    /**
     * Rate limit
     *
     * @return int The number of requests which are allowed in one hour.
     */
    public function getRateLimit() {
        return $this->rateLimit;
    }

    /**
     * Check for rate limit
     *
     * @return bool if there is a rate limit in place
     */
    public function hasRateLimit() {
        return $this->rateLimit !== null && $this->rateLimit !== false;
    }

    /**
     * Remaining requests
     *
     * @return int The number of requests remaining until the rate limit is exceeded.
     */
    public function getRemainingRequests() {
        return $this->remainingRequests;
    }

    /**
     * Rate limit reset time
     *
     * @return DateTime The time the rate limit will be reset
     */
    public function getRateLimitResetTime() {
        return $this->rateLimitResetTime;
    }

    /**
     * Parse the NitrAPI response
     *
     * @param Response $response
     * @return bool|mixed true if response is fine but without message, data or message otherwise.
     * @throws NitrapiHttpErrorException when the API responds with an error message.
     * @throws NitrapiRateLimitException when the user ran into the rate limit.
     */
    public function parseResponse(Response $response) {
        // Rate limit metadata
        if ($response->hasHeader('X-RateLimit-Limit')) {
            $this->rateLimit = $response->getHeader('X-RateLimit-Limit')[0];
            $this->remainingRequests = $response->getHeader('X-RateLimit-Remaining')[0];
            $resetDateTime = new DateTime();
            $resetDateTime->setTimestamp($response->getHeader('X-RateLimit-Reset')[0]);
            $this->rateLimitResetTime = $resetDateTime;

            // We ran into the rate limit, so we throw an exception with all needed information.
            // This gives the client the option to handle that error. To access the rate limit
            // metadata, the getRateLimit(), getRemainingRequests() and getRateLimitResetTime()
            // method can be used at any time.
            if ($response->getStatusCode() === 429) {
                throw new NitrapiRateLimitException($this->getRateLimit(), $this->getRateLimitResetTime());
            }
        } else {
            $this->rateLimit = false;
        }

        $contentType = $response->getHeader('Content-Type')[0];

        // Return plain text
        if (preg_match("/text\\/plain/i", $contentType)) {
            return $response->getBody()->getContents();
        }

        // Parse json
        $json = @json_decode($response->getBody(), true);

        // check for errors in json response
        if (is_array($json) && isset($json['status']) && $json['status'] === 'error') {
            throw new NitrapiHttpErrorException($json["message"]);
        }

        if (isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }

        if (!empty($json['message'])) {
            return $json['message'];
        }

        return true;
    }

    /**
     * @param $url
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function dataGet($url, $headers = null, $options = array()) {
        try {
            if (!isset($options['headers'])) {
                $options['headers'] = [];
            }
            if (is_array($headers)) {
                $options['headers'] = array_merge($options['headers'], $headers);
            }
            if (is_array($options) && isset($options['query'])) {
                $options['query'] = array_merge($options['query'], $this->defaultQuery);
            }
            $this->fillOptions($options);

            $response = $this->request('GET', $url, $options);
            $this->checkErrors($response);
            return $this->parseResponse($response);
        } catch (RequestException $e) {
            $this->handleException($e);
        }
    }

    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function dataPut($url, $body = null, $headers = null, $options = array()) {
        try {
            if (is_array($body)) {
                $options['form_params'] = $body;
            }
            if (is_array($headers)) {
                $options['headers'] = $headers;
            }
            if (is_array($options) && isset($options['query'])) {
                $options['query'] = array_merge($options['query'], $this->defaultQuery);
            }
            $this->fillOptions($options);

            $response = $this->request('PUT', $url, $options);
            $this->checkErrors($response);
            return $this->parseResponse($response);
        } catch (RequestException $e) {
            $this->handleException($e);
        }
    }

    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function dataPost($url, $body = null, $headers = null, $options = array()) {
        try {
            if (is_array($body)) {
                $options['form_params'] = $body;
            }
            if (!isset($options['headers'])) {
                $options['headers'] = [];
            }
            if (is_array($headers)) {
                $options['headers'] = array_merge($options['headers'], $headers);
            }
            if (is_array($options) && isset($options['query'])) {
                $options['query'] = array_merge($options['query'], $this->defaultQuery);
            }
            $this->fillOptions($options);

            $response = $this->request('POST', $url, $options);
            $this->checkErrors($response);
            return $this->parseResponse($response);
        } catch (RequestException $e) {
            $this->handleException($e);
        }
    }

    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @param array $options
     * @return bool
     */
    public function dataDelete($url, $body = null, $headers = null, $options = array()) {
        try {
            if (is_array($body)) {
                $options['form_params'] = $body;
            }
            if (!isset($options['headers'])) {
                $options['headers'] = [];
            }
            if (is_array($headers)) {
                $options['headers'] = array_merge($options['headers'], $headers);
            }
            if (is_array($options) && isset($options['query'])) {
                $options['query'] = array_merge($options['query'], $this->defaultQuery);
            }
            $this->fillOptions($options);

            $response = $this->request('DELETE', $url, $options);
            $this->checkErrors($response);
            return $this->parseResponse($response);
        } catch (RequestException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Exception handling for Nitrapi
     *
     * @param RequestException $e
     * @throws NitrapiConcurrencyException
     * @throws NitrapiHttpErrorException
     * @throws NitrapiMaintenanceException
     */
    protected function handleException(RequestException $e) {
        if ($e->hasResponse()) {
            $response = json_decode($e->getResponse()->getBody(), true);
            $errorId = $e->getResponse()->getHeader('X-Raven-Event-ID');

            $msg = isset($response['message']) ? $response['message'] : 'Unknown error';
            if ($e->getResponse()->getStatusCode() == 503) {
                $exception = new NitrapiMaintenanceException($msg);
                if (!empty($errorId)) $exception->setErrorId($errorId);
                throw $exception;
            }
            if ($e->getResponse()->getStatusCode() == 428) {
                $exception = new NitrapiConcurrencyException($msg);
                if (!empty($errorId)) $exception->setErrorId($errorId);
                throw $exception;
            }
            $exception = new NitrapiHttpErrorException($msg);
            if (!empty($errorId)) $exception->setErrorId($errorId);
            throw $exception;
        }
        $exception = new NitrapiHttpErrorException($e->getMessage());
        if (!empty($errorId)) $exception->setErrorId($errorId);
        throw $exception;
    }

    /**
     * Checks error responses
     *
     * @param Response $response
     * @param int $responseCode
     * @throws NitrapiHttpErrorException
     */
    protected function checkErrors(Response $response, $responseCode = 200) {
        $allowedPorts = array();
        $allowedPorts[] = $responseCode;
        if ($responseCode == 200) {
            $allowedPorts[] = 201;
        }

        if (!in_array($response->getStatusCode(), $allowedPorts)) {
            throw new NitrapiHttpErrorException("Invalid http status code " . $response->getStatusCode());
        }
    }
}