<?php

namespace Nitrapi\Common\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;

class Client extends GuzzleClient
{
    const MINIMUM_PHP_VERSION = '5.3.0';

    public function __construct($baseUrl = '', $config = null) {
        if (PHP_VERSION < self::MINIMUM_PHP_VERSION) {
            throw new NitrapiException(sprintf(
                'You must have PHP version >= %s installed.',
                self::MINIMUM_PHP_VERSION
            ));
        }

        $config['base_url'] = $baseUrl;
        parent::__construct($config);
    }

    /**
     * @param $url
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function dataGet($url, $headers = null, $options = array()) {
        try {
            if (is_array($headers)) {
                $options['headers'] = $headers;
            }

            $request = $this->createRequest('GET', $url, $options);

            $response = $this->send($request);
            $this->checkErrors($response);
            $json = $response->json();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse()->json();
                throw new NitrapiHttpErrorException($response['message']);
            }
            throw new NitrapiHttpErrorException($e->getMessage());
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
    public function dataPost($url, $body = null, $headers = null, $options = array()) {
        try {
            if (is_array($body)) {
                $options['body'] = $body;
            }
            if (is_array($headers)) {
                $options['headers'] = $headers;
            }
            $request = $this->createRequest('POST', $url, $options);

            $response = $this->send($request);
            $this->checkErrors($response);
            $json = $response->json();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse()->json();
                throw new NitrapiHttpErrorException($response['message']);
            }
            throw new NitrapiHttpErrorException($e->getMessage());
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
                $options['body'] = $body;
            }
            if (is_array($headers)) {
                $options['headers'] = $headers;
            }
            $request = $this->createRequest('DELETE', $url, $options);

            $response = $this->send($request);
            $this->checkErrors($response);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse()->json();
                throw new NitrapiHttpErrorException($response['message']);
            }
            throw new NitrapiHttpErrorException($e->getMessage());
        }

        return true;
    }

    protected function checkErrors(Response $response, $responseCode = 200) {
        $json = $response->json();

        $allowedPorts = array();
        $allowedPorts[] = $responseCode;
        if ($responseCode = 200) {
            $allowedPorts[] = 201;
        }

        if (!in_array($response->getStatusCode(), $allowedPorts)) {
            throw new NitrapiHttpErrorException("Invalid http status code " . $response->getStatusCode());
        }

        if (isset($json['status']) && $json['status'] == "error") {
            throw new NitrapiHttpErrorException("Got Error from API " . $json["message"]);
        }
    }
}