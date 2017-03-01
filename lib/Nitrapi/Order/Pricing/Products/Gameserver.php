<?php

namespace Nitrapi\Order\Pricing\Products;

use Nitrapi\Order\Pricing\DimensionPricing;

class Gameserver extends DimensionPricing {

    protected static $product = 'gameserver';
    
    public function setGame($game) {
        $this->additionals['game'] = $game;
    }

    public function setModpack($modpack) {
        $this->additionals['modpack'] = $modpack;
    }
}