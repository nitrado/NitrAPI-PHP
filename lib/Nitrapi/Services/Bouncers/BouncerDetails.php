<?php

namespace Nitrapi\Services\Bouncers;


class BouncerDetails {
    protected $data;

    public function __construct(array &$data) {
        $this->data = $data;
    }

    public function getIdents() {
        return array_map(function($i) {
            return new Ident($i);
        }, $this->data['bouncers']);
    }

    public function getType() {
        return (string)$this->data['type'];
    }

    public function getIdentLimit() {
        return (int)$this->data['max_bouncer'];
    }
}
