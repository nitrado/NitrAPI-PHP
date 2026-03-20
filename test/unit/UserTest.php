<?php

namespace Nitrapi\Tests;

use Nitrapi\Tests\NitrapiTestCase;
use Nitrapi\Nitrapi;

class UserTest extends NitrapiTestCase {
    protected $user;
    
    /**
     * @before
     */
    public function setupUser(): void {
        $nitrapi = $this->nitrapiMock(['user' => []]);
        $this->user = $nitrapi->getCustomer();
    }

    public function testFindAUser(): void {
        $this->assertEquals('Marty', $this->user->getUsername());
        $this->assertEquals(1337, $this->user->getUserId());
        $this->assertEquals(1955, $this->user->getCredit());
        $this->assertEquals('marty.mcfly@biffco.com', $this->user->getEmail());
        $this->assertEquals('eng', $this->user->get('language'));
    }

    public function testPersonalData(): void {
        $this->assertEquals('Marty McFly', $this->user->getPersonalData()['name']);
        $this->assertEquals('DeLorean Street 12', $this->user->getPersonalData()['street']);
        $this->assertEquals('2209', $this->user->getPersonalData()['postcode']);
        $this->assertEquals('Hill Valley', $this->user->getPersonalData()['city']);
    }
}
