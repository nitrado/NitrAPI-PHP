<?php

namespace Nitrapi\Services;

abstract class ServiceItem
{
    protected $service;

    public function __construct(Service $service, array $data) {
        $this->setService($service);
        $this->loadData($data);
    }

    /**
     * @param array $data
     */
    protected function loadData(array $data) {
        $reflectionClass = new \ReflectionClass($this);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            if (!isset($data[$property->getName()])) continue;
            if (!$property->isProtected()) continue;
            $value = $data[$property->getName()];
            if (empty($value)) continue;

            $property->setAccessible(true);
            $property->setValue($this, $value);
            $property->setAccessible(false);
        }
    }

    /**
     * @param Service $service
     */
    protected function setService(Service $service) {
        $this->service = $service;
    }

    /**
     * @return Service
     */
    protected function getService() {
        return $this->service;
    }
}