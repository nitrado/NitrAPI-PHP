<?php

namespace Nitrapi\Services;

use Nitrapi\Nitrapi;

abstract class Service
{
    protected $api;

    protected $id;
    protected $delete_date;
    protected $suspend_date;
    protected $start_date;

    public function __construct(Nitrapi $api, array $data) {
        $this->setApi($api);
        $this->loadData($data);
    }

    /**
     * Returns the suspend date
     *
     * @return \DateTime
     */
    public function getSuspendDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp((int)$this->suspend_date);
        return $datetime;
    }

    /**
     * Returns the delete date
     *
     * @return \DateTime
     */
    public function getDeleteDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp((int)$this->delete_date);
        return $datetime;
    }

    /**
     * Returns the start date
     *
     * @return \DateTime
     */
    public function getStartDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp((int)$this->start_date);
        return $datetime;
    }

    /**
     * Returns the service id
     *
     * @return int
     */
    public function getId() {
        return (int)$this->id;
    }

    /**
     * Returns the user id of the service
     *
     * @return int
     */
    public function getUserId() {
        return (int)$this->user_id;
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUsername() {
        return (string)$this->username;
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