<?php

namespace Nitrapi\Common\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Nitrapi\Common\Exceptions\NitrapiConcurrencyException;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Common\Exceptions\NitrapiMaintenanceException;

class Client extends GuzzleClient
{
    const MINIMUM_PHP_VERSION = '5.5.0';

    protected $defaultQuery = [];

    protected $accessToken = null;

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
            $json = @json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->handleException($e);
        }

        return (isset($json['data'])) ? $json['data'] : $json['message'];
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
            $json = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->handleException($e);
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
            $json = @json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->handleException($e);
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
            $json = @json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->handleException($e);
        }

        if (isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }

        if (!empty($json['message'])) {
            return $json['message'];
        }

        return true;
    }

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

    protected function checkErrors(Response $response, $responseCode = 200) {
        $json = @json_decode($response->getBody(), true);

        if (is_array($json) && isset($json['status']) && $json['status'] == "error") {
            throw new NitrapiHttpErrorException($json["message"]);
        }

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