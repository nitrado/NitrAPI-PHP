<?php

namespace Nitrapi\Domain;

use Nitrapi\Common\Exceptions\NitrapiException;
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
    public function setFqdn($fqdn): self
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
    public function setData(array $data): self
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
    public function getType(): string
    {
        return $this->data['type'];
    }

    /**
     * Get the redirect id
     *
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->data['id'];
    }

    /**
     * Get the redirect sld
     *
     * @return string
     */
    public function getSld(): string
    {
        return $this->data['type'];
    }

    /**
     * Get the redirect target
     *
     * @return string
     */
    public function getTarget(): string
    {
        return $this->data['target'];
    }

    /**
     * Get the redirect pagetitle
     *
     * @return string
     */
    public function getPagetitle(): string
    {
        return $this->data['pagetitle'];
    }

    /**
     * Get the meta description from the redirect
     *
     * @return string
     */
    public function getMetadescr(): string
    {
        return $this->data['metadescr'];
    }

    /**
     * Get the redirect metakey
     *
     * @return string
     */
    public function getMetakey(): string
    {
        return $this->data['metakey'];
    }

    /**
     * Update the redirect
     *
     * @param string|null $type
     * @param string|null $target
     * @param string|null $pagetitle
     * @param string|null $metadescr
     * @param string|null $metakey
     * @return array|bool|string
     * @throws NitrapiException
     */
    public function update(
        ?string $type = null,
        ?string $target = null,
        ?string $pagetitle = null,
        ?string $metadescr = null,
        ?string $metakey = null
    ) {
        $data = [
            'id' => $this->data['id'],
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
     * @throws NitrapiException
     */
    public function delete(): bool
    {
        return $this->getApi()->dataDelete("/domain/" . $this->fqdn . "/redirects", ["id" => $this->getId()]);
    }
}
