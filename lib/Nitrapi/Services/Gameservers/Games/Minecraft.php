<?php

namespace Nitrapi\Services\Gameservers\Games;

class Minecraft extends Game
{
    protected $game = "minecraft";

    public function startChunkfix($world, $limit = 0) {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/chunkfix";
        $this->service->getApi()->dataPost($url, [
            'world' => $world,
            'limit' => $limit,
        ]);

        return true;
    }
}