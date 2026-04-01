<?php

namespace Nitrapi\DynamicShare;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class DynamicShare
{
    protected $api;

    public function __construct(Nitrapi $api)
    {
        $this->setApi($api);
    }

    /**
     * @param string|null $pattern
     * @param int $service_id
     * @return string token
     * @throws NitrapiException
     */
    public function create(?string $pattern = null, int $service_id = 0): string
    {
        $url = "hostsystem/dynamic_share";
        return $this->getApi()->dataPost($url, [
            "pattern" => $pattern,
            "service_id" => $service_id,
        ])['share']['token'];
    }

    /**
     * @throws NitrapiException
     */
    public function attach($token, $user, $password)
    {
        $url = "hostsystem/dynamic_share/attach";
        return $this->getApi()->dataPost($url, [
            "token" => $token,
            "user" => $user,
            "password" => $password,
        ])['share'];
    }

    /**
     * @throws NitrapiException
     */
    public function share($token, $service_id = 0)
    {
        $url = "hostsystem/dynamic_share";
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                "token" => $token,
                "service_id" => $service_id,
            ],
        ])['share'];
    }

    /**
     * @throws NitrapiException
     */
    public function shares($host)
    {
        $url = "hostsystem/dynamic_share/shares";
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                "host" => $host,
            ],
        ])['shares'];
    }

    /**
     * @param Nitrapi $api
     */
    protected function setApi(Nitrapi $api): void
    {
        $this->api = $api;
    }

    /**
     * @return Nitrapi
     */
    public function getApi(): Nitrapi
    {
        return $this->api;
    }
}
