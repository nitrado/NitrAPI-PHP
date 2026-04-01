<?php

namespace Nitrapi\Services\CloudServers\Apps;

/**
 * Class AppDescription
 *
 * @package Nitrapi\Services\CloudServers\Apps
 */
class AppDescription
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string the app type
     */
    public function getAppType(): string
    {
        return $this->data['app_type'];
    }

    /**
     * @return string the category
     */
    public function getCategory(): string
    {
        return $this->data['category'];
    }

    /**
     * @return string the description
     */
    public function getDescription(): string
    {
        return $this->data['description'];
    }

    /**
     * @return bool if the app has IP binding
     */
    public function hasIPBinding(): bool
    {
        return (bool)$this->data['supports_ip_binding'];
    }

    /**
     * @return array all ports for that app
     */
    public function getPorts(): array
    {
        return $this->data['ports'];
    }

    /**
     * @return array the minimum requirements the app needs to fullfill.
     */
    public function getMinimumRequirements(): array
    {
        return $this->data['minimum_requirements'];
    }
}
