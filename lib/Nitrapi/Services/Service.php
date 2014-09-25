<?php

namespace Nitrapi\Services;

use Nitrapi\Nitrapi;

abstract class Service
{
    /**
     * @var $api Nitrapi
     */
    protected $api;
    protected $suspend_date;
    protected $id;

    public function __construct(Nitrapi $api, $data) {
        $this->setApi($api);
        $this->loadData($data);
    }

    public function getSuspendDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp((int)$this->suspend_date);
        return $datetime;
    }

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

        return $this;
    }

    public function getId() {
        return $this->id;
    }

    protected function setApi(Nitrapi $api) {
        $this->api = $api;
    }

    protected function getApi() {
        return $this->api;
    }
}