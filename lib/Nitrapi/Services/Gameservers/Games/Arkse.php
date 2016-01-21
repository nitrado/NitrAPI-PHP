<?php

namespace Nitrapi\Services\Gameservers\Games;

class Arkse extends Game
{
    protected $game = "arkse";
    
    /**
     * Returns the installed ark mods as list
     *
     * @return array
     */
    public function getModList() {
        $url = "services/" . $this->service->getId() . "/gameservers/games/arkse/modlist";
        return $this->service->getApi()->dataGet($url)['modlist'];
    }
}
