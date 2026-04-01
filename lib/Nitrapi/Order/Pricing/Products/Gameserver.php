<?php

namespace Nitrapi\Order\Pricing\Products;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Order\Pricing\DimensionPricing;
use Nitrapi\Order\Pricing\PricingException;
use Nitrapi\Services\Service;
Use Nitrapi\Nitrapi;
use Nitrapi\Order\Pricing\Location;

class Gameserver extends DimensionPricing {

    protected static $product = 'gameserver';
    private $promoCode;

    public function __construct(Nitrapi $nitrapi, Location $location, $promoCode = null) {
        parent::__construct($nitrapi, $location);

        $this->promoCode = $promoCode;
    }

    public function setGame($game): void
    {
        $this->additionals['game'] = $game;
    }

    public function setModpack($modpack): void
    {
        $this->additionals['modpack'] = $modpack;
    }

    /**
     * Set the autoextend status.
     * Important: This does NOT enable autoextend. Use /services/:id/auto_extend instead.
     * @param int $autoextend
     * @return void
     */
    public function setAutoextend(int $autoextend): void
    {
        $this->additionals['autoextend'] = $autoextend;
    }

    /**
     * Returns the price for the service.
     *
     * @param $rentalTime
     * @param Service|null $service
     * @return int
     * @throws PricingException
     * @throws NitrapiException
     */
    public function getPrice($rentalTime, ?Service $service = null): int {
        $price = parent::getPrice($rentalTime, $service);

        if (!empty($this->promoCode)) {
            try {
                $result = $this->nitrapi->dataGet('/order/promo_code/' . $this->promoCode);
                if ($result['promo_code']['effect_type'] === 'DISCOUNT') {
                    $amount = $result['promo_code']['effect_params']['amount'];

                    $price -= ($amount * $price);
                }
            } catch(\Exception $e) {
                //Ignore faulty promo code
            }

        }

        return $price;
    }

    protected function getNewOrderArray($rentalTime): array {
        $orderArray = parent::getNewOrderArray($rentalTime);

        if (!empty($this->promoCode)) {
            $orderArray['promo_code'] = $this->promoCode;
        }

        return $orderArray;
    }
}
