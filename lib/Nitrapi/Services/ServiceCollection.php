<?php

namespace Nitrapi\Services;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class ServiceCollection
{
    /**
     * @var Nitrapi $api
     */
    protected $api;

    /**
     * @var array $services
     */
    protected $services = [];

    public function __construct(Nitrapi $api, array $options = []) {
        $this->setApi($api);

        $res = $this->getApi()->get("services", null, $options)->send();
        if ($res->getStatusCode() != 200) {
            throw new NitrapiException("Can not load services from user");
        }

        $_services = $res->json()['data'];

        if (count($_services) > 0) {
            foreach ($_services as $service) {
                $this->services[] = ServiceFactory::factory($this->getApi(), [
                    'id' => $service['id'],
                ]);
            }
        }
    }

    /**
     * Returns all services
     *
     * @return array
     */
    public function getServices() {
        return $this->services;
    }

    protected function setApi(Nitrapi $api) {
        $this->api = $api;
    }

    protected function getApi() {
        return $this->api;
    }
}