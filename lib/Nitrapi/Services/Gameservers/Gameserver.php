<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Gameservers\CustomerSettings\CustomerSettings;
use Nitrapi\Services\Gameservers\FileServer\FileServer;
use Nitrapi\Services\Gameservers\LicenseKeys\LicenseKeyFactory;
use Nitrapi\Services\Gameservers\MariaDBs\MariaDBFactory;
use Nitrapi\Services\Gameservers\MariaDBs\MariaDB;
use Nitrapi\Services\Gameservers\PluginSystem\PluginSystem;
use Nitrapi\Services\Service;

class Gameserver extends Service
{
    protected $game;
    protected $info = null;

    public function __construct(Nitrapi &$api, &$data) {
        parent::__construct($api, $data);
        $this->info = $this->getApi()->dataGet("services/" . $this->getId() . "/gameservers");
    }

    public function refresh() {
        $url = "services/" . $this->getId() . "/gameservers";
        $this->info = $this->getApi()->dataGet($url);
    }

    /**
     * Returns informations about the gameserver
     *
     * @return mixed
     */
    public function getDetails() {
        return new GameserverDetails($this->info['gameserver']);
    }

    public function getCustomerSettings() {
        return new CustomerSettings($this, $this->info['gameserver']['settings']);
    }

    /**
     * Restarts the gameserver
     *
     * @param string $message
     * @return bool
     */
    public function doRestart($message = null) {
        $url = "services/" . $this->getId() . "/gameservers/restart";
        $this->getApi()->dataPost($url, array(
            'message' => $message
        ));
        return true;
    }

    /**
     * Stopps the gameserver
     *
     * @param string $message
     * @return bool
     */
    public function doStop($message = null) {
        $url = "services/" . $this->getId() . "/gameservers/stop";
        $this->getApi()->dataPost($url, array(
            'message' => $message
        ));
        return true;
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
     * Returns all mariadb databases of the gameserver
     *
     * @return array
     */
    public function getDatabases() {
        $url = "services/" . $this->getId() . "/gameservers/mariadbs";
        $result = $this->getApi()->dataGet($url);
        return $result['databases'];
    }

    /**
     * Returns a mariadb database
     *
     * @param $id
     * @return MariaDB
     */
    public function getDatabase($id) {
        return MariaDBFactory::factory($this, $id);
    }

    /**
     * Deletes a mariadb database
     *
     * @param MariaDB $database
     * @return bool
     */
    public function deleteDatabase(MariaDB $database) {
        $url = "services/" . $this->getId() . "/gameservers/mariadbs/" . $database->getId();
        return $this->getApi()->dataDelete($url);
    }

    /**
     * Returns the license keys of the gameserver
     *
     * @return array
     */
    public function getLicenseKeys() {
        $url = "services/" . $this->getId() . "/gameservers/license_keys";
        $result = $this->getApi()->dataGet($url);
        $return = array();
        if (count($result['keys']) > 0) {
            foreach ($result['keys'] as $key) {
                $return[] = LicenseKeyFactory::factory($this, $key);
            }
        }

        return $return;
    }

    /**
     * Claims all needed license keys and returns all
     * keys
     *
     * @return array
     */
    public function claimLicenseKeys() {
        $url = "services/" . $this->getId() . "/gameservers/license_keys/claim_all";
        $result = $this->getApi()->dataPost($url);
        $return = array();
        if (count($result['keys']) > 0) {
            foreach ($result['keys'] as $key) {
                $return[] = LicenseKeyFactory::factory($this, $key);
            }
        }

        return $return;
    }

    /**
     * Claims all needed license keys and returns all
     * keys
     *
     * @return bool
     */
    public function releaseLicenseKeys() {
        $url = "services/" . $this->getId() . "/gameservers/license_keys/release_all";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Returns the full list of games
     *
     * @return array
     */
    public function getGames() {
        $url = "services/" . $this->getId() . "/gameservers/games";
        return $this->getApi()->dataGet($url);
    }

    /**
     * Installs a new game. Optional with mod pack.
     *
     * @param $game
     * @param null $modpack
     * @return bool
     */
    public function installGame($game, $modpack = null) {
        $url = "services/" . $this->getId() . "/gameservers/games/install";
        $this->getApi()->dataPost($url, array(
            'game' => $game,
            'modpack' => $modpack,
        ));
        return true;
    }

    /**
     * Uninstalls a specific game.
     *
     * @param $game
     * @return bool
     */
    public function uninstallGame($game) {
        $url = "services/" . $this->getId() . "/gameservers/games/uninstall";
        $this->getApi()->dataDelete($url, array(
            'game' => $game,
        ));
        return true;
    }

    /**
     * (Re)starts a specific game.
     *
     * @param $game
     * @return bool
     */
    public function startGame($game) {
        $url = "services/" . $this->getId() . "/gameservers/games/restart";
        $this->getApi()->dataPost($url, array(
            'game' => $game,
        ));
        return true;
    }

    /**
     * Changes the ftp password.
     *
     * @param $password
     * @return bool
     */
    public function changeFTPPassword($password) {
        $url = "services/" . $this->getId() . "/gameservers/ftp/password";
        $this->getApi()->dataPost($url, array(
            'password' => $password,
        ));
        return true;
    }

    /**
     * Changes the mysql password.
     *
     * @param $password
     * @return bool
     */
    public function changeMySQLPassword($password) {
        $url = "services/" . $this->getId() . "/gameservers/mysql/password";
        $this->getApi()->dataPost($url, array(
            'password' => $password,
        ));
        return true;
    }

    /**
     * Reset the mysql database.
     *
     * @param $password
     * @return bool
     */
    public function resetMySQLDatabase() {
        $url = "services/" . $this->getId() . "/gameservers/mysql/reset";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Returns a file server object
     *
     * @return FileServer
     */
    public function getFileServer() {
        return new FileServer($this);
    }

    /**
     * Returns a plugin system object
     *
     * @return PluginSystem
     */
    public function getPluginSystem() {
        return new PluginSystem($this);
    }

    /**
     * Returns the callback handler for the gameserver
     *
     * @return CallbackHandler
     */
    public function getCallbackHandler() {
        return new CallbackHandler($this);
    }
}