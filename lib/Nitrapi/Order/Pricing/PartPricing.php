<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Services\CloudServers\CloudServer;
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

        $multiply = 1;
        $totalPrice = 0;

        // Dynamic rental times
        if ($prices['rental_times'] === null) {
            if(($rentalTime % $prices['min_rental_time']) !== 0) {
                throw new PricingException("Rental time " . $rentalTime . " is invalid (Modulu ".$prices['min_rental_time'].").");
            }

            $multiply = $rentalTime / $prices['min_rental_time'];
            $rentalTime = $prices['min_rental_time'];
        }

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

            if (!is_float($bestPrice) && !is_int($bestPrice)) throw new PricingException("No valid price found for part {$part['type']}.");
            $totalPrice += $bestPrice;
        }

        // Multiple by rental time if dynamic rental times
        $totalPrice *= $multiply;

        // Remove 50% of advice if the old service is not a Cloud Server Dynamic
        if (!($service instanceof CloudServer && $service->getDetails()->isDynamic())) {
            $totalPrice = $this->calcAdvicePrice(round($totalPrice, 0), $prices['advice']);
        }

        return $totalPrice;
    }

    public function checkDependencies() {
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