<?php

namespace Nitrapi\GameInfo;

use Nitrapi\Common\NitrapiObject;

class GameInfoInterface extends NitrapiObject {

    public function getGameList() {
        $api = $this->getApi();
        $result = [];

        $gameList = $api->dataGet('/gameserver/games')['games']['games'];

        foreach ($gameList as $gameListEntry) {
            $result[$gameListEntry['id']] = new GameInfo($api, $gameListEntry);
        }

        return $result;
    }

    public function getGameListByFolderShort() {
        $result = [];
        $gameList = $this->getGameList();

        foreach ($gameList as $gameInfo) {
            $result[$gameInfo->getFolderShort()] = $gameInfo;
        }

        return $result;
    }
}
