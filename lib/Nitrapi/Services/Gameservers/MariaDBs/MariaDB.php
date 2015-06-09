<?php

namespace Nitrapi\Services\Gameservers\MariaDBs;

use Nitrapi\Services\Gameservers\Gameserver;
use Nitrapi\Services\ServiceItem;

class MariaDB extends ServiceItem
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    protected $id;
    protected $username;
    protected $password;
    protected $schema;
    protected $hostname;

    public function __construct(Gameserver &$service, array &$data) {
        parent::__construct($service, $data);
        $this->setService($service);

    }

    public function getId() {
        return (int)$this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSchema() {
        return $this->schema;
    }

    public function getHostname() {
        return $this->hostname;
    }

    /**
     * Imports a sql from uri
     *
     * @param $uri
     * @return bool
     */
    public function import($uri) {
        $url = "/services/".$this->getService()->getId()."/gameservers/mariadbs/".$this->getId()."/import";
        $this->getService()->getApi()->dataPost($url, array(
            "uri" => $uri
        ));
        return true;
    }
}