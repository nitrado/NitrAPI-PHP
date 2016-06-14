<?php

namespace Nitrapi\Order\Pricing;

abstract class DimensionPricing extends Pricing {
    
    public function getPrices($location_id) {
        return $this->nitrapi->dataGet("/order/pricing/" . $this->type, null, [
            'query' => [
                'location' => $location_id
            ]
        ])['prices'];
    }
    
}