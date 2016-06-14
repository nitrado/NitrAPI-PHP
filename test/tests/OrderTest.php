<?php

namespace Nitrapi\Tests;

use Nitrapi\Tests\NitrapiTestCase;
use Nitrapi\Nitrapi;
use Nitrapi\Payment\Order;

class OrderTest extends NitrapiTestCase {
    function testOrderProcessing() {
        $nitrapi = $this->nitrapiMock([
            'payment.order.prices' => [],
            'order.process' => [],
        ]);

        $order = new Order($nitrapi);
        $this->assertTrue($order->process(
            720,
            ['cpu' => 1, 'ram' => 1, 'ssd' => 10],
            9,
            2,
            'cloud_server'
        ));
    }
}