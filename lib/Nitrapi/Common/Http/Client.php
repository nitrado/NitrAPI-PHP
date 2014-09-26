<?php

namespace Nitrapi\Common\Http;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Curl\CurlVersion;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Message\Response;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;

class Client extends GuzzleClient
{
    const VERSION = '1.0.0';
    const MINIMUM_PHP_VERSION = '5.3.0';

    public function __construct($baseUrl = '', $config = null) {
        //todo later implement rabbitmq
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

    /**
     * @param $url
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function dataGet($url, $headers = null, $options = array()) {
        try {
            $res = $this->get($url, $headers, $options)->send();
            $json = $res->json();
            $this->checkErrors($res);
        } catch (ServerErrorResponseException $e) {
            $response = $e->getResponse()->json();
            throw new NitrapiHttpErrorException($response['message']);
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
    public function dataPost($url, $body = null, $headers = null, $options = []) {
        try {
            $res = $this->post($url, $headers, $body, $options)->send();
            $json = $res->json();
            $this->checkErrors($res, 201);
        } catch (ServerErrorResponseException $e) {
            throw new NitrapiHttpErrorException($e->getResponse()->json()['message']);
        }

        return (isset($json['data'])) ? $json['data'] : $json['message'];
    }

    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @param array $options
     * @return bool
     */
    public function dataDelete($url, $body = null, $headers = null, $options = []) {
        try {
            $res = $this->delete($url, $headers, $body, $options)->send();
            $this->checkErrors($res, 204);
        } catch (ServerErrorResponseException $e) {
            throw new NitrapiHttpErrorException($e->getResponse()->json()['message']);
        }

        return true;
    }

    protected function checkErrors(Response $response, $responseCode = 200) {
        $json = $response->json();

        if ($response->getStatusCode() != $responseCode) {
            throw new NitrapiHttpErrorException("Invalid http status code " . $response->getStatusCode());
        }

        if (isset($json['status']) && $json['status'] == "error") {
            throw new NitrapiHttpErrorException("Got Error from API " . $json["message"]);
        }
    }
}