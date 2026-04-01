<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Common\Exceptions\NitrapiException;

class Banlist
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
     * Get the ban list of the game server.
     *
     * @return array
     * @throws NitrapiException
     */
    public function getBanlist(): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/banlist";
        return $this->service->getApi()->dataGet($url);
    }

    /**
     * Adds a player to the ban list.
     *
     * @param $identifier
     * @return string
     * @throws NitrapiException
     */
    public function addBanlist($identifier): string
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/banlist";
        return $this->service->getApi()->dataPost($url, [
            'identifier' => $identifier,
        ]);
    }

    /**
     * Removes a player from the ban list.
     *
     * @param $identifier
     * @return string
     * @throws NitrapiException
     */
    public function removeBanlist($identifier): string
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/banlist";
        return $this->service->getApi()->dataDelete($url, [
            'identifier' => $identifier,
        ]);
    }
}
