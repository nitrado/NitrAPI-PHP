<?php

namespace Nitrapi\Services\Bouncers;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Bouncer extends Service
{
    protected $info;

    /**
     * @throws NitrapiException
     */
    public function __construct(Nitrapi $api, $id)
    {
        parent::__construct($api, $id);

        if ($this->isActive()) {
            $this->info = $this->getApi()->dataGet($this->url())['bouncer'];
        }
    }

    public function getDetails(): BouncerDetails
    {
        return new BouncerDetails($this->info);
    }

    /**
     * @throws NitrapiException
     */
    public function addIdent($identName, $password)
    {
        return $this->getApi()->dataPost($this->url(), [
            'ident' => $identName,
            'password' => $password,
        ]);
    }

    /**
     * @throws NitrapiException
     */
    public function editPassword(Ident $ident, $newPassword)
    {
        return $this->getApi()->dataPut($this->url(), [
            'ident' => $ident->getIdent(),
            'password' => $newPassword,
        ]);
    }

    /**
     * @throws NitrapiException
     */
    public function deleteIdent(Ident $ident)
    {
        return $this->getApi()->dataDelete($this->url(), [
            'ident' => $ident->getIdent(),
        ]);
    }

    private function url(): string
    {
        return 'services/' . $this->getId() . '/bouncers';
    }
}
