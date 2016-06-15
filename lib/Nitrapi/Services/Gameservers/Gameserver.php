<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Gameservers\Addons\AddonManager;
use Nitrapi\Services\Gameservers\Addons\Addons;
use Nitrapi\Services\Gameservers\ApplicationServer\ApplicationServer;
use Nitrapi\Services\Service;
use Nitrapi\Services\Gameservers\Games\Game;
use Nitrapi\Services\Gameservers\MariaDBs\MariaDB;
use Nitrapi\Services\Gameservers\FileServer\FileServer;
use Nitrapi\Services\Gameservers\TaskManager\TaskManager;
use Nitrapi\Services\Gameservers\MariaDBs\MariaDBFactory;
use Nitrapi\Services\Gameservers\PluginSystem\PluginSystem;
use Nitrapi\Services\Gameservers\LicenseKeys\LicenseKeyFactory;
use Nitrapi\Common\Exceptions\NitrapiServiceTypeNotFoundException;
use Nitrapi\Services\Gameservers\CustomerSettings\CustomerSettings;

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
     * @return GameserverDetails
     */
    public function getDetails() {
        return new GameserverDetails($this->info['gameserver']);
    }

    /**
     * Returns available features from the gameserver
     *
     * @return GameserverFeatures
     */
    public function getFeatures() {
        return new GameserverFeatures($this->info['gameserver']['game_specific']['features']);
    }

    public function getCustomerSettings() {
        return new CustomerSettings($this, $this->info['gameserver']['settings']);
    }

    /**
     * Restarts the gameserver
     *
     * @param string $message
     * @param string $restartMessage
     * @return bool
     */
    public function doRestart($message = null, $restartMessage = null) {
        $url = "services/" . $this->getId() . "/gameservers/restart";
        $this->getApi()->dataPost($url, array(
            'message' => $message,
            'restart_message' => $restartMessage,
        ));
        return true;
    }

    /**
     * Stopps the gameserver
     *
     * @param string $message
     * @param string $stopMessage
     * @return bool
     */
    public function doStop($message = null, $stopMessage = null) {
        $url = "services/" . $this->getId() . "/gameservers/stop";
        $this->getApi()->dataPost($url, array(
            'message' => $message,
            'stop_message' => $stopMessage,
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
        $data =  array(
            'game' => $game,
        );
        if (!empty($modpack)) $data['modpack'] = $modpack;
        $this->getApi()->dataPost($url, $data);
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
        $url = "services/" . $this->getId() . "/gameservers/games/start";
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
     * Returns a app server object
     *
     * @return ApplicationServer
     */
    public function getApplicationServer() {
        return new ApplicationServer($this);
    }

    /**
     * Get access to the addons, if the gameserver has any.
     * 
     * @return Addons
     */
    public function getAddons() {
        return new AddonManager($this);
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

    /**
     * Returns the task manager
     *
     * @return TaskManager
     */
    public function getTaskManager() {
        return new TaskManager($this);
    }

    /**
     * Returns the admin logs
     *
     * @return array
     */
    public function getAdminLogs() {
        $url = "services/" . $this->getId() . "/gameservers/admin_logs";
        return $this->getApi()->dataGet($url);
    }

    /**
     * Returns the stats of the last x hours
     * Default: 24 hours
     *
     * @param int $hours
     * @return array
     */
    public function getStats($hours = 24) {
        $url = "services/" . $this->getId() . "/gameservers/stats";
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                'hours' => $hours
            ]
        ])['stats'];
    }

    /**
     * Sends a command directly into the game server
     *
     * @param $command
     * @return bool
     */
    public function sendCommand($command) {
        $url = "services/" . $this->getId() . "/gameservers/command";
        $this->getApi()->dataPost($url, [
            'command' => $command
        ]);

        return true;
    }

    /**
     * Returns a game instance
     *
     * @param $game
     * @return Game
     * @throws NitrapiServiceTypeNotFoundException
     */
    public function getGame($game) {
        $class = "Nitrapi\\Services\\Gameservers\\Games\\" . ucfirst($game);

        if (!class_exists($class)) {
            throw new NitrapiServiceTypeNotFoundException("Game class " . $game . " not found");
        }

        return new $class($this);
    }

    /**
     * Updates a managed root setting
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function changeManagedRootSetting($key, $value) {
        $url = "services/" . $this->getId() . "/gameservers/managed_root/" . $key;
        $this->getApi()->dataPost($url, [
            $key => $value
        ]);

        return true;
    }
}