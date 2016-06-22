<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Services\Service;

abstract class PartPricing extends Pricing {
    
    protected $parts = null;

    public function addPart($part, $value) {
        if ($this->parts === null) {
            $this->getParts();
        }

        if (!array_key_exists($part, $this->parts)) {
            throw new PricingException("Part " . $part . " is not available for this product.");
        }
        
        $this->parts[$part] = $value;
    }

    public function getParts() {
        if ($this->parts === null) {
            $prices = $this->getPrices();
            $this->parts = [];
            foreach ($prices['parts'] as $part) {
                $this->parts[$part['type']] = null;
            }
        }

        return $this->parts;
    }

    public function getPrice($rentalTime, Service &$service = null) {
        $this->checkDependencies();
        $prices = $this->getPrices($service);
        $parts = $this->getParts();

        $totalPrice = 0;

        foreach ($prices['parts'] as $part) {
            $amount = $parts[$part['type']];
            if (empty($amount)) continue;

            if ($amount <= 0) throw new PricingException("The amount of {$part['type']} can't be 0.");
            if ($amount > $part['max_count']) throw new PricingException("The amount {$amount} of type {$part['type']} is too big.");
            if ($amount < $part['min_count']) throw new PricingException("The amount {$amount} of type {$part['type']} is too low.");
            if (!empty($part['steps']) && !in_array($amount, $part['steps'])) throw new PricingException("The amount {$amount} of type {$part['type']} is not available.");

            $bestPrice = false;
            foreach ($part['rental_times'] as $hoursAndPrices)
                if ($hoursAndPrices['hours'] == $rentalTime)
                    foreach ($hoursAndPrices['prices'] as $price)
                        if ($price['count'] === $amount)
                            $bestPrice = $price['price'];

            if (!is_int($bestPrice)) throw new PricingException("No valid price found for part {$part['type']}.");
            $totalPrice += $bestPrice;
        }

        return $totalPrice;
    }

    protected function checkDependencies() {
        $prices = $this->getPrices();
        $parts = $this->getParts();
        foreach ($prices['parts'] as $part) {
            if ($part['optional'] === false &&
                (!isset($parts[$part['type']]) || empty($parts[$part['type']]))) {
                throw new PricingException("No value provided for needed part type " . $part['type'] . ".");
            }
        }
    }
}