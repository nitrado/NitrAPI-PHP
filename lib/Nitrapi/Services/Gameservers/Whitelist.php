<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Common\Exceptions\NitrapiException;

class Whitelist
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
     * Returns the whitelist of the game server.
     *
     * @return array
     * @throws NitrapiException
     */
    public function getWhitelist(): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/whitelist";
        return $this->service->getApi()->dataGet($url);
    }

    /**
     * Adds a player to the whitelist.
     *
     * @param $identifier
     * @return string
     * @throws NitrapiException
     */
    public function addWhitelist($identifier): string
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/whitelist";
        return $this->service->getApi()->dataPost($url, [
            'identifier' => $identifier,
        ]);
    }

    /**
     * Removes a player from the whitelist.
     *
     * @param $identifier
     * @return string
     * @throws NitrapiException
     */
    public function removeWhitelist($identifier): string
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/whitelist";
        return $this->service->getApi()->dataDelete($url, [
            'identifier' => $identifier,
        ]);
    }
}
