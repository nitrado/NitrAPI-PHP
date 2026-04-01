<?php

namespace Nitrapi\Services\Gameservers\CustomerSettings;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\Gameservers\Gameserver;

class CustomerSettings
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    protected $settings;

    protected $defaults;

    public function __construct(Gameserver $service, array &$settings)
    {
        $this->service = $service;
        $this->settings = &$settings;
    }

    /**
     * Return the default value for a customer setting. If no parameter is
     * provided, all defaults (with the appropriate categories) will be returned.
     * If a category is provided, all settings in this category will be
     * returned. If category and key is provided, the actual value is
     * returned.
     *
     * @param string|null $category The setting category
     * @param string|null $key The setting key
     * @return array|string The default values
     *
     * @throws CustomerSettingNotFoundException
     * @throws NitrapiException
     * @see CustomerSettingsDBSetting::setDefaultValue()
     *
     */
    public function getDefaults(?string $category = null, ?string $key = null)
    {
        // Refresh the cache.
        if ($this->defaults === null) {
            $this->defaults = $this->service->getApi()->dataGet(
                'services/' . $this->service->getId() . '/gameservers/settings/defaults',
            )['settings'];
        }

        if ($category !== null && !isset($this->defaults[$category])) {
            throw new CustomerSettingNotFoundException('Category "' . $category . '" not found');
        }

        if ($key !== null && !isset($this->defaults[$category][$key])) {
            throw new CustomerSettingNotFoundException(
                'Setting "' . $key . '" in category "' . $category . '" not found',
            );
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

    /**
     * @throws CustomerSettingNotFoundException
     */
    public function readSetting($category = null, $key = null)
    {
        if (!empty($category) && !$this->hasCategory($category)) {
            throw new CustomerSettingNotFoundException("Category \"" . $category . "\" not found");
        }

        if (!empty($key) && !$this->hasSetting($category, $key)) {
            throw new CustomerSettingNotFoundException(
                "Setting \"" . $key . "\" in category \"" . $category . "\" not found",
            );
        }

        if (!empty($category) && !empty($key)) {
            return $this->settings[$category][$key];
        }

        if (!empty($category)) {
            return $this->settings[$category];
        }

        return $this->settings;
    }

    /**
     * @throws NitrapiException
     * @throws CustomerSettingNotFoundException
     */
    public function writeSetting($category, $key, $value): bool
    {
        if (!$this->hasSetting($category, $key)) {
            throw new CustomerSettingNotFoundException(
                "Setting \"" . $key . "\" in category \"" . $category . "\" not found",
            );
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

    /**
     * @throws NitrapiException
     */
    public function getConfigSets()
    {
        return $this->service->getApi()->dataGet(
            "services/" . $this->service->getId() . "/gameservers/settings/sets",
        )['sets'];
    }

    /**
     * @throws NitrapiException
     */
    public function restoreConfigset($id): bool
    {
        $this->service->getApi()->dataPost(
            "services/" . $this->service->getId() . "/gameservers/settings/sets/" . $id . "/restore",
        );
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function deleteConfigset($id): bool
    {
        $this->service->getApi()->dataDelete(
            "services/" . $this->service->getId() . "/gameservers/settings/sets/" . $id,
        );
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function createConfigset($name = null): bool
    {
        $settings = (!empty($name)) ? ['name' => $name] : [];

        $this->service->getApi()->dataPost(
            "services/" . $this->service->getId() . "/gameservers/settings/sets",
            $settings,
        );
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function resetSettings(): bool
    {
        $this->service->getApi()->dataDelete("services/" . $this->service->getId() . "/gameservers/settings");
        return true;
    }

    public function hasCategory($category): bool
    {
        if (!isset($this->settings[$category])) {
            return false;
        }

        return true;
    }

    public function hasSetting($category, $key): bool
    {
        if (!$this->hasCategory($category)) {
            return false;
        }

        if (!isset($this->settings[$category][$key])) {
            return false;
        }

        return true;
    }
}
