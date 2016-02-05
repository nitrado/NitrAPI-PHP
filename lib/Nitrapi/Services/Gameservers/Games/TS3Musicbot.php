<?php

namespace Nitrapi\Services\Gameservers\Games;

class TS3Musicbot extends Game
{
    protected $game = "ts3musicbot";

    /**
     * Reinstalls the TS3Musicbot
     *
     * @return bool
     */
    public function doReinstall() {
        $url = "services/" . $this->service->getId() . "/gameservers/games/ts3musicbot/reinstall";
        $this->service->getApi()->dataPost($url);

        return true;
    }

}