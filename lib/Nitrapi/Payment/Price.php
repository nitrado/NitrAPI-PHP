<?php

namespace Nitrapi\Payment;

use Nitrapi\Payment\PricePart;
use Nitrapi\Common\Exceptions\NitrapiPaymentException;

class Price
{
    protected $basePrice = 0;
    protected $rentalTimes = [];
    protected $parts = [];
    
    public function __construct($basePrice, $rentalTimes) {
        $this->basePrice = $basePrice;
        $this->rentalTimes = $rentalTimes;
    }

    public function addPart(PricePart $part) {
        $this->parts[] = $part;
    }

    public function getParts() {
        return $this->parts;
    }

    public function getRentalTimes() {
        return $this->rentalTimes;
    }

    public function getBasePrice() {
        return $this->basePrice;
    }

    /**
     * Calculate the best price for the given parts and a time. This
     * parts are an array of type and count of this type. Here is an
     * example:
     *
     * array(
     *   'ram' => 2,
     *   'cpu' => 1,
     *   'ssd' => 20
     * )
     * 
     * @param $rentalTime The time period for rental
     * @param $parts In which you want to calculate the price
     */
    public function getBestPrice($rentalTime, array $parts=[]) {
        if (!in_array($rentalTime, $this->rentalTimes)) throw new NitrapiPaymentException("Wrong rental time {$rentalTime} given.");

        $combinedPrice = $this->basePrice;
        foreach ($this->parts as $part) {
            if (in_array($part->getType(), array_keys($parts)))
                $combinedPrice += $part->getBestPrice($rentalTime, $parts[$part->getType()]);
            elseif ($part->isRequired())
                throw new NitrapiPaymentException("Missing part {$part->getType()}, can't calculate price.");
        }

        return $combinedPrice;
    }
}