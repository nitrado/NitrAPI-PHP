<?php

namespace Nitrapi\Steam\Workshop;

class Workshop
{
    protected $steam;

    public function __construct(\Nitrapi\Steam\Steam $steam) {
        $this->setSteam($steam);
    }
    
    protected function setSteam(\Nitrapi\Steam\Steam $steam) {
        $this->steam = $steam;
    }

    public function getSteam() {
        return $this->steam;
    }
    
    public function getItemInfo($ids) {
        $reponse = $this->getSteam()->getApi()->dataGet("/steam_workshop/item_info", null, array("query" => array("ids" => implode(',', $ids))));
        return $reponse['workshop_items'];
    }
}
