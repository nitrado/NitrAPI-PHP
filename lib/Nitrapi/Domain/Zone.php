<?php

namespace Nitrapi\Domain;

use DateTime;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class Zone extends NitrapiObject
{

    /**
     * @var $fqdn
     */
    protected $fqdn;

    /**
     * @var $data array
     */
    protected $data;

    public function __construct(Nitrapi $api, $fqdn, array $data = [])
    {
        parent::__construct($api);
        $this->setData($data);
        $this->setFqdn($fqdn);
    }

    /**
     * Sets data field $data to specified value.
     *
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        if (count($data) > 0) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Sets fqdn field $fqdn to specified value.
     *
     * @param $fqdn
     * @return $this
     */
    public function setFqdn($fqdn)
    {
        $this->fqdn = $fqdn;

        return $this;
    }

    /**
     * Returns DNSSEC status
     *
     * @return bool
     */
    public function getDNSSEC()
    {
        return (bool) $this->data['DNSSEC'];
    }

    /**
     * Sets the DNSSEC status
     *
     * @param bool $dnssec
     * @return mixed
     */
    public function setDNSSEC($dnssec)
    {
        $data = [
            "dnssec" => $dnssec
        ];
        return $this->getApi()->dataPut("/domain/" . $this->fqdn . "/zone", $data);
    }


}
