<?php

namespace Nitrapi\Services\Gameservers\LicenseKeys;

use Nitrapi\Services\Gameservers\Gameserver;

class LicenseKeyFactory
{
    public static function factory(Gameserver $service, array &$data): LicenseKey
    {
        return new LicenseKey($service, $data);
    }
}
