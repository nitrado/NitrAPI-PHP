<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Gameserver extends Service
{
    protected $game;

    public function __construct(Nitrapi $api, $data) {
         parent::__construct($api, $data);
    }

    public function createDatabase($options = []) {
        return [];
    }

    public function getDatabases() {
        return [];
    }

    public function deleteDatabase($id) {
        return true;
    }
}