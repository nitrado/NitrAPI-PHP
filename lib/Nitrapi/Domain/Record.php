<?php

namespace Nitrapi\Domain;

use DateTime;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class Record extends NitrapiObject
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
     * Sets data field $data to specified value.
     *
     * @param $data
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
     * Returns if the record is set by the customer
     *
     * @return bool
     */
    public function setByUser()
    {
        return $this->data['mode'] == "manual";
    }

    /**
     * Changes the record
     *
     * @param string $name
     * @param string $type
     * @param string $content
     * @param int $ttl
     * @return mixed
     */
    public function update($name, $type, $content, $ttl)
    {
        $data = [
            "name_old" => $this->getName(),
            "type_old" => $this->getType(),
            "content_old" => $this->getContent(),
            "ttl" => $ttl,
            "name" => $name,
            "content" => $content,
            "type" => $type
        ];
        return $this->getApi()->dataPut("/domain/" . $this->fqdn . "/records", $data);
    }

    /**
     * Return the record name
     *
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * Return the record Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->data['type'];
    }

    /**
     * Return the record Content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->data['content'];
    }

    /**
     * Deletes the record
     *
     * @return bool
     */
    public function delete()
    {
        $data = [
            "name" => $this->getName(),
            "type" => $this->getType(),
            "content" => $this->getContent()
        ];
        return $this->getApi()->dataDelete("/domain/" . $this->fqdn . "/records", $data);
    }


}
