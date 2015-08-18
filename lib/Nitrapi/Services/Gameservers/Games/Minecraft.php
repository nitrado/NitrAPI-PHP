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
     * Changing some overview map settings
     *
     * @param bool|false $enabled
     * @param bool|true $signs
     * @param $
     * @return bool
     */
    public function setOverviewMap($enabled = false, $signs = true, $ipsonly = null, $reset = false) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/chunkfix";
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


}