<?php

namespace Nitrapi\Domain;

use DateTime;
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
     * Returns the domain id
     *
     * @return int
     */
    public function getId() {
        return $this->data['id'];
    }

    /**
     * Returns status of domain
     *
     * @return string
     */
    public function getStatus() {
        return $this->data['status'];
    }

    /**
     * Returns DateTime of domain deletion
     *
     * @return DateTime|null
     */
    public function getDeleteAt() {
        if (empty($this->data['delete_at'])) {
            return null;
        }

        return (new DateTime())->setTimestamp(strtotime($this->data['delete_at']));
    }

    /**
     * Returns the shorthand of the registrar
     *
     * @return string
     */
    public function getProvider() {
        return $this->data['provider'];
    }

    /**
     * Returns integer whether domain will be deleted when it expires
     *
     * @return int
     */
    public function deleteOnExpire() {
        return $this->data['delete_on_expire'];
    }

    public function setDomain($domain) {
        $this->data['domain'] = $domain;

        return $this;
    }

    /**
     * Returns the FQDN
     *
     * @return string
     */
    public function getDomain() {
        return $this->data['domain'];
    }

    /**
     * Returns the date until the domain is able to renew.
     *
     * @return DateTime
     */
    public function getRenewUntil() {
        return (new DateTime())->setTimestamp(strtotime($this->data['renew_until']));
    }

    /**
     * Returns the date until the domain has been paid.
     *
     * @return DateTime
     */
    public function getPaidUntil() {
        return (new DateTime())->setTimestamp(strtotime($this->data['paid_until']));
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

    /**
     * Returns array of DNS Records
     *
     * @return array
     */
    public function getDNSRecords() {
        return $this->data['dns_records'];
    }

    /**
     * Returns boolean true if
     *
     * @return mixed
     */
    public function hasDns() {
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
     * Requesting the Auth Code removes the transfer lock of the domain.
     *
     * @return string
     */
    public function createAuthCode() {
        return $this->getApi()->dataPost('/domain/'.$this->getDomain().'/auth_code');
    }

    /**
     * Delete the Auth Code.
     * If you delete the Auth Code, the transfer lock will be re-enabled again.
     *
     * @return string
     */
    public function deleteAuthCode() {
        return $this->getApi()->dataDelete('/domain/'.$this->getDomain().'/auth_code');
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

    /**
     * Returns the corresponding service id.
     *
     * @return int
     */
    public function getServiceId() {
        return $this->data['service_id'];
    }

    /**
     * Returns the expire date.
     *
     * @return DateTime
     */
    public function getExpireDate() {
        return (new DateTime())->setTimestamp(strtotime($this->data['expires']));
    }

    /**
     * Returns the tld ID.
     *
     * @return int
     */
    public function getTldId() {
        return $this->data['tld_id'];
    }

    /**
     * Returns all current notifications
     *
     * @return array
     */
    public function getNotifications(): array {
        return $this->getApi()->dataGet("/domain/" . $this->getDomain() . "/notifications");
    }
}
