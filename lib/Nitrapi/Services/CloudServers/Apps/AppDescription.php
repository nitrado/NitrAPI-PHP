<?php

namespace Nitrapi\Services\CloudServers\Apps;

/**
 * Class AppDescription
 *
 * @package Nitrapi\Services\CloudServers\Apps
 */
class AppDescription {
    public $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * @return string the app type
     */
    public function getAppType() {
        return $this->data['app_type'];
    }

    /**
     * @return string the category
     */
    public function getCategory() {
        return $this->data['category'];
    }

    /**
     * @return string the description
     */
    public function getDescription() {
        return $this->data['description'];
    }

    /**
     * @return bool if the app has IP binding
     */
    public function hasIPBinding() {
        return (bool)$this->data['supports_ip_binding'];
    }

    /**
     * @return array all ports for that app
     */
    public function getPorts() {
        return $this->data['ports'];
    }
}
