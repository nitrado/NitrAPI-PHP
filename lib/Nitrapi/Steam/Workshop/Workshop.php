<?php

namespace Nitrapi\Steam\Workshop;

use Nitrapi\Steam\Steam;

class Workshop
{
    protected $steam;

    public function __construct(Steam $steam)
    {
        $this->setSteam($steam);
    }

    protected function setSteam(Steam $steam): void
    {
        $this->steam = $steam;
    }

    public function getSteam()
    {
        return $this->steam;
    }

    public function getItemInfo($ids)
    {
        $response = $this->getSteam()->getApi()->dataGet(
            "/steam_workshop/item_info",
            null,
            ["query" => ["ids" => implode(',', $ids)]],
        );
        return $response['workshop_items'];
    }
}
