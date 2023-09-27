<?php

namespace Nitrapi\Domain;

use DateTime;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class Redirect extends NitrapiObject
{

    /**
     * @var $api Nitrapi
     */
    protected $api;

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
     * Get the redirect type
     *
     * @return string
     */
    public function getType()
    {
        return $this->data['type'];
    }

    /**
     * Get the redirect id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->data['id'];
    }

    /**
     * Get the redirect sld
     *
     * @return string
     */
    public function getSld()
    {
        return $this->data['type'];
    }

    /**
     * Get the redirect target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->data['target'];
    }

    /**
     * Get the redirect pagetitle
     *
     * @return string
     */
    public function getPagetitle()
    {
        return $this->data['pagetitle'];
    }

    /**
     * Get the meta description from the redirect
     *
     * @return string
     */
    public function getMetadescr()
    {
        return $this->data['metadescr'];
    }

    /**
     * Get the redirect metakey
     *
     * @return string
     */
    public function getMetakey()
    {
        return $this->data['metakey'];
    }

    /**
     * Update the redirect
     *
     * @param string $type
     * @param string $target
     * @param string $pagetitle
     * @param string $metadescr
     * @param string $metakey
     * @return mixed
     */
    public function update($type = NULL, $target = NULL, $pagetitle = NULL, $metadescr = NULL, $metakey = NULL)
    {
        $data = [
            'id' => $this->data['id']
        ];

        if (!is_null($type)) {
            $data['type'] = $type;
        }
        if (!is_null($target)) {
            $data['target'] = $target;
        }
        if (!is_null($pagetitle)) {
            $data['pagetitle'] = $pagetitle;
        }
        if (!is_null($metadescr)) {
            $data['metadescr'] = $metadescr;
        }
        if (!is_null($metakey)) {
            $data['metakey'] = $metakey;
        }

        return $this->getApi()->dataPut("/domain/" . $this->fqdn . "/redirects", $data);
    }

    /**
     * Delete the redirect
     *
     * @return bool
     */
    public function delete()
    {
        return $this->getApi()->dataDelete("/domain/" . $this->fqdn . "/redirects", ["id" => $this->getId()]);
    }


}
