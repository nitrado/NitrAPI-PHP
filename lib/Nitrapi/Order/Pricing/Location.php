<?php

namespace Nitrapi\Order\Pricing;

class Location {
    
    protected $data = [];
    
    public function __construct(array $data) {
        $this->data = $data;
    }

    public function getId() {
        return $this->data['id'];
    }

    public function getCountry() {
        return $this->data['country'];
    }

    public function getCity() {
        return $this->data['city'];
    }

    public function getProducts() {
        return $this->data['products'];
    }
}