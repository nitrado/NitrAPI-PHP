<?php

namespace Nitrapi\Domain;

use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class Domain extends NitrapiObject
{

    const AUTH_CODE_STATUS_AVAILABLE = 'available';
    const AUTH_CODE_STATUS_NOT_REQUESTED = 'not_requested';
    const AUTH_CODE_STATUS_PENDING = 'pending';

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

    public function setData($data) {
        if (count($data) > 0) {
            $this->data = $data;
        }

        return $this;
    }

    public function getProvider() {
        return $this->data['provider'];
    }

    public function setDomain($domain) {
        $this->data['domain'] = $domain;

        return $this;
    }

    /**
     * Return the full domain.
     *
     * @return string
     */
    public function getDomain() {
        return $this->data['domain'];
    }

    /**
     * Returns the date until the domain is able to renew.
     *
     * @return string
     */
    public function getRenewUntil() {
        return (new \DateTime())->setTimestamp(strtotime($this->data['renew_until']));
    }

    /**
     * Returns the date until the domain has been paid.
     *
     * @return string
     */
    public function getPaidUntil() {
        return (new \DateTime())->setTimestamp(strtotime($this->data['paid_until']));
    }

    /**
     * Sets new nameserver for the domain.
     * If no nameserver has been set, the default settings will be restored.
     *
     * @param null $nameserver
     * @return mixed
     */
    public function setNameserver($nameserver = null) {
        $data = [];

        if (!empty($nameserver)) {
            foreach ($nameserver as $key => $ns) {
                $ns_id = $key + 1;
                $data['nameserver' . $ns_id] = $ns;
            }
        }

        $result = $this->getApi()->dataPut('/domain/'.$this->getDomain().'/nameserver', $data);
        $this->data['nameserver'] = $nameserver;
        return $result;
    }

    /**
     * Returns the current nameserver list.
     *
     * @return array
     */
    public function getNameserver() {
        return $this->data['nameserver'];
    }

    public function getDNSRecords() {
        return $this->data['dns_records'];
    }

    /**
     * Extends the Domain instantly.
     *
     * @return string
     */
    public function doExtend() {
        return $this->getApi()->dataPost('/domain/'.$this->getDomain().'/extend');
    }

    /**
     * Returns the Auth Code and the Auth Code status.
     *
     * @return array
     */
    public function getAuthCode() {
        return $this->getApi()->dataGet('/domain/'.$this->getDomain().'/auth_code');
    }

    /**
     * Requesting the Auth Code.
     * It can take up to 24 hours until the Auth Code is available.
     *
     * @return array
     */
    public function createAuthCode() {
        return $this->getApi()->dataPost('/domain/'.$this->getDomain().'/auth_code');
    }

    public function changeHandle(Handle $handle, $type = HandleManager::TYPE_OWNER_C) {
        return $this->getApi()->dataPut('/domain/'.$this->getDomain().'/handle/' . $handle->getHandle(), [
            'type' => $type
        ]);
    }

    /**
     * This method returns true if the domain is available to be registered.
     *
     * @return bool
     */
    public function isFree() {
        return $this->getApi()->dataGet('/domain/'.$this->getDomain().'/check')['check']['free'];
    }

}