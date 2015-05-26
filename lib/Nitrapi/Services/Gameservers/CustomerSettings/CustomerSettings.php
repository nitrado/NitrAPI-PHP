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
        $this->service = $service;
        $this->settings = $settings;
    }

    public function readSetting($category = null, $key = null) {
        if (!empty($category) && !$this->hasCategory($category)) {
            throw new CustomerSettingNotFoundException("Category \"".$category."\" not found");
        }

        if (!empty($key) && !$this->hasSetting($category, $key)) {
            throw new CustomerSettingNotFoundException("Setting \"".$key."\" in category \"".$category."\" not found");
        }

        if (!empty($category) && !empty($key)) {
            return $this->settings[$category][$key];
        }

        if (!empty($category)) {
            return $this->settings[$category];
        }

        return $this->settings;
    }

    public function writeSetting($category, $key, $value) {
        if (!$this->hasSetting($category, $key)) {
            throw new CustomerSettingNotFoundException("Setting \"".$key."\" in category \"".$category."\" not found");
        }

        $this->service->getApi()->dataPost("services/" . $this->service->getId() . "/gameservers/settings", [
            "category" => $category,
            "key" => $key,
            "value" => $value,
        ]);

        return true;
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