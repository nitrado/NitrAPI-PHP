<?php

namespace Nitrapi\TopLevelDomain;

use DateTime;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class TopLevelDomain extends NitrapiObject
{

    const PROVIDER_CPS = 'cps';
    const PROVIDER_NICDIRECT = 'nicdirect';
    const PROVIDER_EXTERNAL = 'external';

    /**
     * @var $api Nitrapi
     */
    protected $api;

    /**
     * @var $data array
     */
    protected $data;

    public function __construct(Nitrapi &$api,  array $data = []) {
        parent::__construct($api);
        $this->setData($data);
    }

    /**
     * Sets data field $data to specified value.
     *
     * @param $data
     * @return $this
     */
    public function setData($data) {
        if (count($data) > 0) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Return the id of the Tld
     *
     * @return int
     */
    public function getId(): int {
        return (int)$this->data['id'];
    }

    /**
     * Returns the tld name
     *
     * @return string
     */
    public function getTld(): string {
        return $this->data['tld'];
    }

}
