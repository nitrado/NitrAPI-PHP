<?php

namespace Nitrapi\GameInfo;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\NitrapiObject;

class GameInfoInterface extends NitrapiObject
{
    /**
     * @throws NitrapiException
     */
    public function getGameList(): array
    {
        $api = $this->getApi();
        $result = [];

        $gameList = $api->dataGet('/gameserver/games')['games']['games'];

        foreach ($gameList as $gameListEntry) {
            $result[$gameListEntry['id']] = new GameInfo($api, $gameListEntry);
        }

        return $result;
    }

    /**
     * @throws NitrapiException
     */
    public function getGameListByFolderShort(): array
    {
        $result = [];
        $gameList = $this->getGameList();

        foreach ($gameList as $gameInfo) {
            $result[$gameInfo->getFolderShort()] = $gameInfo;
        }

        return $result;
    }
}
