<?php

namespace Nitrapi\Payment;

use Nitrapi\Nitrapi;
use Nitrapi\Common\Exceptions\NitrapiPaymentException;

class PricePart
{
    protected $type = false;
    protected $minCount = 0;
    protected $maxCount = 0;
    protected $steps = [];
    protected $isRequired = true;
    protected $rentalTimes = [];
    protected $priceStructure = false;
    
    public function __construct(Array $pricePartStructure) {
        $this->priceStructure = $pricePartStructure;
        $this->type = $pricePartStructure['type'];
        $this->minCount = $pricePartStructure['min_count'];
        $this->maxCount = $pricePartStructure['max_count'];
        $this->steps = $pricePartStructure['steps'];
        $this->isRequired = !$pricePartStructure['optional'];
        $this->rentalTimes = $pricePartStructure['rental_times'];
    }

    public function getPriceStructure() {
        return $this->priceStructure;
    }

    public function getType() {
        return $this->type;
    }

    public function getSteps() {
        return $this->steps;
    }

    public function getMinCount() {
        return $this->minCount;
    }

    public function getMaxCount() {
        return $this->maxCount;
    }

    public function getRentalTimes() {
        return $this->rentalTimes;
    }

    public function isRequired() {
        return $this->isRequired;
    }

    public function getBestPrice($rentalTime, $amount) {
        if ($amount <= 0) throw new NitrapiPaymentException("The amount of {$this->type} can't be 0.");
        if ($amount > $this->maxCount) throw new NitrapiPaymentException("The amount {$amount} of type {$this->type} is too big.");
        if ($amount < $this->minCount) throw new NitrapiPaymentException("The amount {$amount} of type {$this->type} is too low.");
        if (!empty($this->steps) && !in_array($amount, $this->steps)) throw new NitrapiPaymentException("The amount {$amount} of type {$this->type} is not available.");

        $bestPrice = false;
        foreach ($this->rentalTimes as $hoursAndPrices)
            if ($hoursAndPrices['hours'] == $rentalTime)
                foreach ($hoursAndPrices['prices'] as $price)
                    if ($price['count'] <= $amount && (!$bestPrice || $price['price'] < $bestPrice))
                        $bestPrice = $amount*$price['price'];

        if (!$bestPrice) throw new NitrapiPaymentException("No valid price found for part {$this->type}.");
        return $bestPrice;
    }
}