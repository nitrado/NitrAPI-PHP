<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Services\Service;

abstract class DimensionPricing extends Pricing {

    protected $dimensions = null;

    public function addDimenstion($dimension, $value) {
        if ($this->dimensions === null) {
            $this->getDimensions();
        }

        if (!array_key_exists($dimension, $this->dimensions)) {
            throw new PricingException("Dimension " . $dimension . " is not available for this product.");
        }

        $this->dimensions[$dimension] = $value;
    }

    public function getDimensions() {
        if ($this->dimensions === null) {
            $prices = $this->getPrices();
            $this->dimensions = [];
            foreach ($prices['dimensions'] as $dimension) {
                $this->dimensions[$dimension['id']] = null;
            }
        }

        return $this->dimensions;
    }
    
    public function getPrice($rentalTime, Service &$service = null) {
        $information = $this->getPrices($service);
        $dimensions = $this->getDimensions();
        $dimensions['rental_time'] = $rentalTime;

        $prices = $information['prices'];
        foreach ($dimensions as $key => $value) {
            if ($value === null) continue;
            if (array_key_exists($value, $prices)) {
                $prices = $prices[$value];
            } else {
                throw new PricingException("No dimension information for " . $value . " found.");
            }
        }

        if (is_array($prices) && isset($prices['price'])) {
            $price = (int)$prices['price'];
            $advice = $information['advice'];

            return $this->calcAdvicePrice($price, $advice);
        }

        throw new PricingException("No price for selected dimensions not found.");
    }
}