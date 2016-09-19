<?php

namespace Nitrapi\Services;

use Nitrapi\Nitrapi;

abstract class Service
{
    protected $api;

    protected $id;
    protected $status;
    protected $user_id;
    protected $username;
    protected $delete_date;
    protected $suspend_date;
    protected $start_date;
    protected $details;
    protected $websocket_token;
    protected $roles;

    const SERVICE_STATUS_INSTALLING = 'installing';
    const SERVICE_STATUS_ACTIVE = 'active';
    const SERVICE_STATUS_SUSPENDED = 'suspended';
    const SERVICE_STATUS_DELETED = 'deleted';
    const SERVICE_STATUS_ADMINLOCKED = 'adminlocked';
    const SERVICE_STATUS_ADMINLOCKED_SUSPENDED = 'adminlocked_suspended';

    public function __construct(Nitrapi &$api, array &$data) {
        $this->setApi($api);
        $this->loadData($data);
    }

    /**
     * Returns the service status
     * 
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Returns the suspend date
     *
     * @return \DateTime
     */
    public function getSuspendDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp((int)$this->suspend_date);
        return $datetime;
    }

    /**
     * Returns the delete date
     *
     * @return \DateTime
     */
    public function getDeleteDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp((int)$this->delete_date);
        return $datetime;
    }

    /**
     * Returns the start date
     *
     * @return \DateTime
     */
    public function getStartDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp((int)$this->start_date);
        return $datetime;
    }

    /**
     * Returns the service id
     *
     * @return int
     */
    public function getId() {
        return (int)$this->id;
    }

    /**
     * Returns the user id of the service
     *
     * @return int
     */
    public function getUserId() {
        return (int)$this->user_id;
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUsername() {
        return (string)$this->username;
    }

    /**
     * Returns the websocket token
     *
     * @return string
     */
    public function getWebsocketToken() {
        return (string)$this->websocket_token;
    }

    /**
     * Returns all service details
     *
     * @return array
     */
    public function getServiceDetails() {
        return (array)$this->details;
    }

    /**
     * Returns the roles of the service
     *
     * @return array
     */
    public function getRoles() {
        return (array)$this->roles;
    }

    /**
     * Returns the ddos history
     *
     * @return array
     */
    public function getDDoSHistory() {
        $url = "services/" . $this->getId() . "/ddos";
        return $this->getApi()->dataGet($url);
    }

    /**
     * Returns the last log entries. You can optionally
     * provide a page number.
     *
     * @param int $hours
     * @return array
     */
    public function getLogs($page = 1) {
        $url = "services/" . $this->getId() . "/logs";
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                'page' => $page
            ]
        ]);
    }

    /**
     * Adds a new log entry to your service
     *
     * @param string $category
     * @param string $message
     * @return array
     */
    public function addLog($category, $message) {
        $url = "services/" . $this->getId() . "/logs";
        $this->getApi()->dataPost($url, [
            'category' => $category,
            'message' => $message
        ]);
        return true;
    }

    /**
     * @param array $data
     */
    protected function loadData(array $data) {
        $reflectionClass = new \ReflectionClass($this);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            if (!isset($data[$property->getName()])) continue;
            if (!$property->isProtected()) continue;
            $value = $data[$property->getName()];
            if (empty($value)) continue;

            $property->setAccessible(true);
            $property->setValue($this, $value);
            $property->setAccessible(false);
        }
    }

    /**
     * @param Nitrapi $api
     */
    protected function setApi(Nitrapi $api) {
        $this->api = $api;
    }

    /**
     * @return Nitrapi
     */
    public function getApi() {
        return $this->api;
    }
}