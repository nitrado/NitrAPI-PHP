<?php

namespace Nitrapi\Services\Gameservers\Games;

use Nitrapi\Common\Exceptions\NitrapiException;

class Arkse extends Game
{
    protected $game = "arkse";

    /**
     * Returns the installed ark mods as list
     *
     * @return array
     * @throws NitrapiException
     */
    public function getModList(): array
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/arkse/modlist";
        return $this->service->getApi()->dataGet($url)['modlist'];
    }
}
