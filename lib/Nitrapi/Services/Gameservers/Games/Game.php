<?php

namespace Nitrapi\Services\Gameservers\Games;

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

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }

    public function getGame() {
        return $this->game;
    }

    /**
     * @return mixed
     * @throws \Nitrapi\Common\Exceptions\NitrapiHttpErrorException
     */
    public function getInfo() {
        $url = "services/" . $this->service->getId() . "/gameservers/games/" . $this->getGame();
        return $this->service->getApi()->dataGet($url);
    }
}