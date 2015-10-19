<?php

namespace Nitrapi\Services\Gameservers\Games;

class Minecraft extends Game
{
    protected $game = "minecraft";

    /**
     * Starting the minecraft chunkfix with given world
     *
     * @param $world
     * @param int $limit
     * @return bool
     */
    public function startChunkfix($world, $limit = 0) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/chunkfix";
        $this->service->getApi()->dataPost($url, [
            'world' => $world,
            'limit' => $limit,
        ]);

        return true;
    }

    /**
     * Rendering overview map
     *
     * @return bool
     */
    public function renderOverviewMap() {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/overviewmap_render";
        $this->service->getApi()->dataPost($url);

        return true;
    }

    /**
     * Return the minecraft overview map log file
     *
     * @return mixed
     */
    public function getOverviewMapLog() {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/overviewmap_log";
        $result = $this->service->getApi()->dataGet($url);

        if (isset($result['log'])) {
            return $result['log'];
        }

        return null;
    }

    /**
     * Changing some overview map settings
     *
     * @param bool|false $enabled
     * @param bool|true $signs
     * @param $
     * @return bool
     */
    public function setOverviewMap($enabled = false, $signs = true, $ipsonly = null, $reset = false) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/overviewmap";
        $this->service->getApi()->dataPost($url, [
            'enabled' => (int)$enabled,
            'signs' => (int)$signs,
            'reset' => (int)$reset,
            'ipsonly' => $ipsonly,
        ]);

        return true;
    }

    /**
     * Changing bungeecord settings
     *
     * @param bool|false $enabled
     * @param bool|false $only
     * @param string $firewall
     * @param null $ip
     * @return bool
     */
    public function setBungeeCord($enabled = false, $only = false, $firewall = 'off', $ip = null) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/bungeecord";
        $this->service->getApi()->dataPost($url, [
            'enabled' => (int)$enabled,
            'only' => (int)$only,
            'firewall' => $firewall,
            'firewall_ip' => $ip,
        ]);

        return true;
    }

    /**
     * Changing rtk settings
     *
     * @param bool|false $enabled
     * @param null $username
     * @param null $password
     * @return bool
     */
    public function setRemoteToolkit($enabled = false, $username = null, $password = null) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/rtk";
        $this->service->getApi()->dataPost($url, [
            'enabled' => (int)$enabled,
            'username' => $username,
            'password' => $password,
        ]);

        return true;
    }

    /**
     * Changing mcmyadmin settings
     *
     * @param bool|false $enabled
     * @param null $username
     * @param null $password
     * @param null $language
     * @return bool
     */
    public function setMcMyAdmin($enabled = false, $username = null, $password = null, $language = null) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/mcmyadmin";
        $this->service->getApi()->dataPost($url, [
            'enabled' => (int)$enabled,
            'username' => $username,
            'password' => $password,
            'language' => $language,
        ]);

        return true;
    }

    /**
     * Creates a new backup of a specified world
     *
     * @param $world
     * @return bool
     */
    public function createBackup($world) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/backup";
        $this->service->getApi()->dataPost($url, [
            'world' => $world
        ]);

        return true;
    }

    /**
     * Deletes a specified backup
     *
     * @param $backup
     * @return bool
     */
    public function deleteBackup($backup) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/backup/" . $backup;
        $this->service->getApi()->dataDelete($url);

        return true;
    }

    /**
     * Restore a specified backup
     *
     * @param $backup
     * @return bool
     */
    public function restoreBackup($backup) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/backup/" . $backup . "/restore";
        $this->service->getApi()->dataPost($url);

        return true;
    }

    /**
     * Installs a specific Minecraft Version
     *
     * @param $md5
     * @return bool
     */
    public function switchVersion($md5) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/change_version";
        $this->service->getApi()->dataPost($url, [
            'md5' => $md5
        ]);

        return true;
    }

    /**
     * Returns the formated UUID id of the specific minecraft user
     *
     * @param $username
     * @return array
     */
    public function getUUID($username) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/uuid";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'username' => $username
            ]
        ])['user'];
    }

    /**
     * Returns the avatar as base64 encoded content of the specific minecraft user
     * Note: case sensitive!
     *
     * @param $username
     * @return array
     */
    public function getAvatar($username) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/avatar";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'username' => $username
            ]
        ])['user'];
    }
}