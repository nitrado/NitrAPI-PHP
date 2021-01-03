<?php

namespace Nitrapi\Tests;

use Nitrapi\Nitrapi;
use Nitrapi\Tests\NitrapiTestCase;

class UserTest extends NitrapiTestCase {
    protected $user;
    
    /**
     * @before
     */
    function setupUser() {
        $nitrapi = $this->nitrapiMock(['user' => []]);
        $this->user = $nitrapi->getCustomer();
    }

    function testFindAUser() {
        $this->assertEquals('Marty', $this->user->getUsername());
        $this->assertEquals(1337, $this->user->getUserId());
        $this->assertEquals(1955, $this->user->getCredit());
        $this->assertEquals('marty.mcfly@biffco.com', $this->user->getEmail());
        $this->assertEquals('eng', $this->user->getLanguage());
    }

    function testPersonalData() {
        $this->assertEquals('Marty McFly', $this->user->getPersonalData()['name']);
        $this->assertEquals('DeLorean Street 12', $this->user->getPersonalData()['street']);
        $this->assertEquals('2209', $this->user->getPersonalData()['postcode']);
        $this->assertEquals('Hill Valley', $this->user->getPersonalData()['city']);
    }
}