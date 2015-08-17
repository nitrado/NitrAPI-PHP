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
}