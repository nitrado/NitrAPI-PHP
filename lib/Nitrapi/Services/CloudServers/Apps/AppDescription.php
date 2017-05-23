<?php

namespace Nitrapi\Services\CloudServers\Apps;

class AppDescription {
    public $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function getAppType() {
        return $this->data['app_type'];
    }

    public function getCategory() {
        return $this->data['category'];
    }

    public function getDescription() {
        return $this->data['description'];
    }

    public function hasIPBinding() {
        return $this->data['supports_ip_binding'];
    }

    public function getPorts() {
        return $this->data['ports'];
    }
}
