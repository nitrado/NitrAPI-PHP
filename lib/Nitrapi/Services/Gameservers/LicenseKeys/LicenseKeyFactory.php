<?php

namespace Nitrapi\Services\Gameservers\LicenseKeys;

use Nitrapi\Services\Gameservers\Gameserver;

class LicenseKeyFactory
{
    public static function factory(Gameserver $service, array &$data) {
        return new LicenseKey($service, $data);
    }
}
