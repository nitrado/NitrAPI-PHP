<?php

namespace Nitrapi\Services;

abstract class ServiceItem
{
    protected $service;

    public function __construct(Service $service, array $data)
    {
        $this->setService($service);
        $this->loadData($data);
    }

    /**
     * @param array $data
     */
    protected function loadData(array $data): void
    {
        $reflectionClass = new \ReflectionClass($this);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            if (!isset($data[$property->getName()])) {
                continue;
            }
            if (!$property->isProtected()) {
                continue;
            }
            $value = $data[$property->getName()];
            if ($value === null) {
                continue;
            }

            if (PHP_VERSION_ID >= 80100) {
                $property->setValue($this, $value);
            } else {
                $property->setAccessible(true);
                $property->setValue($this, $value);
                $property->setAccessible(false);
            }
        }
    }

    protected function setService(Service $service): void
    {
        $this->service = $service;
    }

    protected function getService(): Service
    {
        return $this->service;
    }
}
