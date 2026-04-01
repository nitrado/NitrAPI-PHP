<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Common\Exceptions\NitrapiException;

class Adminlist
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
     * Returns the admin list from the game server.
     *
     * @return array
     * @throws NitrapiException
     */
    public function getAdminlist(): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/adminlist";
        return $this->service->getApi()->dataGet($url);
    }

    /**
     * Adds a player as admin to the game server.
     *
     * @param $identifier
     * @return string
     * @throws NitrapiException
     */
    public function addAdminlist($identifier): string
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/adminlist";
        return $this->service->getApi()->dataPost($url, [
            'identifier' => $identifier,
        ]);
    }

    /**
     * Removes a admin player from the game server.
     *
     * @param $identifier
     * @return string
     * @throws NitrapiException
     */
    public function removeAdminlist($identifier): string
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/adminlist";
        return $this->service->getApi()->dataDelete($url, [
            'identifier' => $identifier,
        ]);
    }
}
