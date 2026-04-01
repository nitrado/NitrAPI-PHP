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

    /**
     * @throws NitrapiException
     */
    public function __construct(Nitrapi $api, array $options = [])
    {
        $this->setApi($api);

        $_services = $this->getApi()->dataGet("services", null, $options);
        if (count($_services['services']) > 0) {
            foreach ($_services['services'] as $service) {
                try {
                    $this->services[] = ServiceFactory::factory($this->getApi(), [
                        'id' => $service['id'],
                    ]);
                } catch (\Exception $e) {
                }
            }
        }
    }

    /**
     * Returns all services
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    protected function setApi(Nitrapi $api): void
    {
        $this->api = $api;
    }

    protected function getApi(): Nitrapi
    {
        return $this->api;
    }
}
