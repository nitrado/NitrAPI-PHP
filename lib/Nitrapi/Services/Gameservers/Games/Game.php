<?php

namespace Nitrapi\Services\Gameservers\Games;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\Gameservers\Gameserver;

abstract class Game
{
    /**
     * @var Gameserver
     */
    protected $service;

    /**
     * @var string
     */
    protected $game;

    public function __construct(Gameserver $service)
    {
        $this->service = $service;
    }

    public function getGame(): string
    {
        return $this->game;
    }

    /**
     * @return array|bool|string
     * @throws NitrapiException
     */
    public function getInfo()
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/" . $this->getGame();
        return $this->service->getApi()->dataGet($url);
    }
}
