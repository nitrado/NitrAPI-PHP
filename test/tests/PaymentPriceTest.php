<?php

namespace Nitrapi\Tests;

use Nitrapi\Tests\NitrapiTestCase;
use Nitrapi\Nitrapi;
use Nitrapi\Payment\Price;
use Nitrapi\Payment\PricePart;

class PaymentPriceTest extends NitrapiTestCase {
    private $price = null;

    function setUp() {
        $nitrapi = $this->nitrapiMock([
            'service3' => [],
            'gameservers' => [],
            'payment.order.prices' => []
        ]);

        $service = $nitrapi->getService(['id' => 3]);
        $this->price = $service->getPriceStructure();
    }

    function testPriceInstance() {
        $this->assertInstanceOf('Nitrapi\Payment\Price', $this->price);
    }

    /**
     * @expectedException Nitrapi\Common\Exceptions\NitrapiPaymentException
     */
    function testForMissingParts() {
        $this->price->getBestPrice(72, []);
    }

    /**
     * @expectedException Nitrapi\Common\Exceptions\NitrapiPaymentException
     */
    function testWrongMaxAmount() {
        $this->price->getBestPrice(72, ['ram' => 100, 'cpu' => 1, 'ssd' => 10]);
    }

    /**
     * @expectedException Nitrapi\Common\Exceptions\NitrapiPaymentException
     */
    function testWrongMinAmount() {
        $this->price->getBestPrice(72, ['ram' => 0, 'cpu' => 1, 'ssd' => 10]);
    }

    /**
     * @expectedException Nitrapi\Common\Exceptions\NitrapiPaymentException
     */
    function testWrongAmountStep() {
        $this->price->getBestPrice(72, ['ram' => 1, 'cpu' => 1, 'ssd' => 19]);
    }

    /**
     * @expectedException Nitrapi\Common\Exceptions\NitrapiPaymentException
     */
    function testWrongRentalTime() {
        $this->price->getBestPrice(1337, ['ram' => 1, 'cpu' => 1, 'ssd' => 20]);
    }

    /**
     * @expectedException Nitrapi\Common\Exceptions\NitrapiPaymentException
     */
    function testWrongAmount() {
        $this->price->getBestPrice(72, ['ram' => 1, 'cpu' => 99, 'ssd' => 20]);
    }

    function testSimplePriceCalculations() {
        $this->assertEquals(200+150+80+100, $this->price->getBestPrice(72, [
            'ram' => 1, 'cpu' => 1, 'ssd' => 10
        ]));
        $this->assertEquals(200+(2*150)+80+100, $this->price->getBestPrice(72, [
            'ram' => 2, 'cpu' => 1, 'ssd' => 10
        ]));
        $this->assertEquals(200+(5*120)+80+100, $this->price->getBestPrice(72, [
            'ram' => 5, 'cpu' => 1, 'ssd' => 10
        ]));
        $this->assertEquals(200+(5*120)+80+100+100, $this->price->getBestPrice(72, [
            'ram' => 5, 'cpu' => 1, 'ssd' => 10, 'windows' => 1
        ]));
    }
}