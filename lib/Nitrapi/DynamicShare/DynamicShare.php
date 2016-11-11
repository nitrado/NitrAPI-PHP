<?php

namespace Nitrapi\DynamicShare;

use Nitrapi\Nitrapi;

class DynamicShare {
    protected $api;

    public function __construct(Nitrapi $api) {
        $this->setApi($api);
    }

    /**
     * @param string $pattern
     * @param int $service_id
     * @return string token
     */
    public function create($pattern = null, $service_id = 0) {
        $url = "hostsystem/dynamic_share";
        return $this->getApi()->dataPost($url, [
            "pattern" => $pattern,
            "service_id" => $service_id
        ])['share']['token'];
    }

    public function share($token, $service_id = 0) {
        $url = "hostsystem/dynamic_share";
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                "token" => $token,
                "service_id" => $service_id
            ]
        ])['share'];
    }

    public function shares($host) {
        $url = "hostsystem/dynamic_share/shares";
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                "host" => $host
            ]
        ])['shares'];
    }

    /**
     * @param Nitrapi $api
     */
    protected function setApi(Nitrapi $api) {
        $this->api = $api;
    }

    /**
     * @return Nitrapi
     */
    public function getApi() {
        return $this->api;
    }
}