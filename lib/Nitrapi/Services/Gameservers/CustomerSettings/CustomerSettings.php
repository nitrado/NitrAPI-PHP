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

    protected $defaults = null;

    public function __construct(Gameserver &$service, array &$settings) {
        $this->service = $service;
        $this->settings = $settings;
    }

    /**
     * Return the default value for a customer setting. If no parameter is
     * provided, all defaults (with the appropriate categories) will be returned.
     * If a category is provided, all settings in this category will be
     * returned. If category and key is provided, the actual value is
     * returned.
     *
     * @see CustomerSettingsDBSetting::setDefaultValue()
     *
     * @param string|null $category The setting category
     * @param string|null $key The setting key
     * @return array|string The default values
     *
     * @throws CustomerSettingNotFoundException
     */
    public function getDefaults($category=null, $key=null) {
        // Refresh the cache.
        if ($this->defaults === null) {
            $this->defaults = $this->service->getApi()->dataGet('services/' . $this->service->getId() . '/gameservers/settings/defaults')['settings'];
        }

        if ($category !== null && !isset($this->defaults[$category])) {
            throw new CustomerSettingNotFoundException('Category "'.$category.'" not found');
        }

        if ($key !== null && !isset($this->defaults[$category][$key])) {
            throw new CustomerSettingNotFoundException('Setting "'.$key.'" in category "'.$category.'" not found');
        }

        // Return a single default value
        if ($category !== null && $key !== null) {
            return $this->defaults[$category][$key];
        }

        // Return a whole category
        if ($category !== null) {
            return $this->defaults[$category];
        }

        // Return all default values with categories
        return $this->defaults;
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

        // Update internal storage
        $this->settings[$category][$key] = $value;

        return true;
    }

    public function getConfigSets() {
        return $this->service->getApi()->dataGet("services/" . $this->service->getId() . "/gameservers/settings/sets")['sets'];
    }

    public function restoreConfigset($id) {
        $this->service->getApi()->dataPost("services/" . $this->service->getId() . "/gameservers/settings/sets/".$id."/restore");
        return true;
    }

    public function deleteConfigset($id) {
        $this->service->getApi()->dataDelete("services/" . $this->service->getId() . "/gameservers/settings/sets/".$id);
        return true;
    }

    public function createConfigset($name = null) {
        $settings = (!empty($name)) ? ['name' => $name] : [];

        $this->service->getApi()->dataPost("services/" . $this->service->getId() . "/gameservers/settings/sets", $settings);
        return true;
    }

    public function resetSettings() {
        $this->service->getApi()->dataDelete("services/" . $this->service->getId() . "/gameservers/settings");
        return true;
    }

    public function hasCategory($category) {
        if (!isset($this->settings[$category])) {
            return false;
        }

        return true;
    }

    public function hasSetting($category, $key) {
        if (!$this->hasCategory($category)) {
            return false;
        }

        if (!isset($this->settings[$category][$key])) {
            return false;
        }

        return true;
    }
}
