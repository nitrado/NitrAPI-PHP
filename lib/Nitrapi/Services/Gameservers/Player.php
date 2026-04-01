<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Common\Exceptions\NitrapiException;

class Player
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver $service)
    {
        $this->service = $service;
    }

    /**
     * Return a list of all online players.
     *
     * @return array
     * @throws NitrapiException
     */
    public function getOnlinePlayers(): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/players";
        return $this->service->getApi()->dataGet($url);
    }
}
