<?php

namespace Nitrapi\Services\Gameservers\CustomerSettings;

use Nitrapi\Services\Gameservers\Gameserver;

class CustomerSettings
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    protected $settings = null;

    public function __construct(Gameserver &$service, array &$settings) {
        $this->settings = $settings;
    }

    public function readSetting($category = null, $key = null) {
        if (!empty($category) && !$this->hasCategory($category)) {
            throw new CustomerSettingNotFoundException("Category \"".$category."\" not found");
        }

        if (!empty($key) && !$this->hasSetting($category, $key)) {
            throw new CustomerSettingNotFoundException("Setting \"".$key."\" in category \"".$category."\" not found");
        }

        if (empty($category) && empty($key)) {
            return $this->settings;
        }

        if (empty($category)) {
            return $this->settings[$category];
        }

        return $this->settings[$category][$key];
    }

    public function writeSetting($category, $key) {
        //todo
    }

    protected function hasCategory($category) {
        if (!isset($this->settings[$category])) {
            return false;
        }

        return true;
    }

    protected function hasSetting($category, $key) {
        if (!$this->hasCategory($category)) {
            return false;
        }

        if (!isset($this->settings[$category][$key])) {
            return false;
        }

        return true;
    }
}