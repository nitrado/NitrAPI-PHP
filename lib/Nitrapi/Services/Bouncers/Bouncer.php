<?php

namespace Nitrapi\Services\Bouncers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Bouncer extends Service {
    protected $info;

    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);

        if ($this->isActive()) {
            $this->info = $this->getApi()->dataGet($this->url())['bouncer'];
        }
    }

    public function getDetails() {
        return new BouncerDetails($this->info);
    }

    public function addIdent($identName, $password) {
        return $this->getApi()->dataPost($this->url(), [
            'ident' => $identName,
            'password' => $password
        ]);
    }

    public function editPassword(Ident $ident, $newPassword) {
        return $this->getApi()->dataPut($this->url(), [
            'ident' => $ident->getIdent(),
            'password' => $newPassword,
        ]);
    }

    public function deleteIdent(Ident $ident) {
        return $this->getApi()->dataDelete($this->url(), [
            'ident' => $ident->getIdent()
        ]);
    }

    private function url($path = '') {
        return 'services/' . $this->getId() . '/bouncers' . $path;
    }
}
