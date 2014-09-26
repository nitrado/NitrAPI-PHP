<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Gameservers\MariaDBs\MariaDBFactory;
use Nitrapi\Services\Gameservers\MariaDBs\MariaDB;
use Nitrapi\Services\Service;

class Gameserver extends Service
{
    protected $game;

    public function __construct(Nitrapi $api, $data) {
         parent::__construct($api, $data);
    }

    /**
     * @param array $credentials
     * @param array $options
     * @return MariaDB
     */
    public function createDatabase($credentials = array(), $options = array()) {
        $url = "services/" . $this->getId() . "/gameservers/mariadbs";

        $result = $this->getApi()->dataPost($url, $credentials, null, $options);
        return MariaDBFactory::factory($this, $result['database']['id']);
    }

    /**
     * @return array
     */
    public function getDatabases() {
        $url = "services/" . $this->getId() . "/gameservers/mariadbs";
        $result = $this->getApi()->dataGet($url);
        return $result['databases'];
    }

    /**
     * @param $id
     * @return MariaDB
     */
    public function getDatabase($id) {
        return MariaDBFactory::factory($this, $id);
    }

    /**
     * @param MariaDB $database
     * @return bool
     */
    public function deleteDatabase(MariaDB $database) {
        $url = "services/" . $this->getId() . "/gameservers/mariadbs/" . $database->getId();
        return $this->getApi()->dataDelete($url);
    }
}