<?php

namespace Nitrapi\Services\Gameservers;


use DateTime;

class GameserverDetails
{
    protected $data;

    public function __construct(array &$data)
    {
        $this->data = $data;
    }

    /**
     * Returns the current gameserver status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->data['status'];
    }

    /**
     * @return bool
     */
    public function isManagedRoot(): bool
    {
        return isset($this->data['managed_root']);
    }

    /**
     * @return array
     */
    public function getManagedRoot(): array
    {
        if (!$this->isManagedRoot()) {
            return [];
        }
        return $this->data['managed_root'];
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string)$this->data['username'];
    }

    /**
     * Returns the gameserver ip address
     *
     * @return string
     */
    public function getIP(): string
    {
        return (string)$this->data['ip'];
    }

    /**
     * Return the IPv6 address if available
     *
     * If there is an IPv6 address attached to the gameserver, it will return
     * the IPv6 as a string. Otherwise, NULL is returned.
     *
     * @return string|null
     */
    public function getIPv6(): ?string
    {
        return $this->data['ipv6'];
    }

    /**
     * Returns the gameserver port
     *
     * @return int
     */
    public function getPort(): int
    {
        return (int)$this->data['port'];
    }

    /**
     * Returns the gameserver query port
     *
     * @return int
     */
    public function getQueryPort(): int
    {
        return (int)$this->data['query_port'];
    }

    /**
     * Returns the gameserver rcon port
     *
     * @return int
     */
    public function getRconPort(): int
    {
        return (int)$this->data['rcon_port'];
    }

    /**
     * Returns true if the gameserver is in minecraft mode
     *
     * @return bool
     */
    public function isMinecraftMode(): bool
    {
        return (bool)$this->data['minecraft_mode'];
    }

    /**
     * Returns the game
     *
     * @return string
     */
    public function getGame(): string
    {
        return (string)$this->data['game'];
    }

    /**
     * Returns the installed modpacks
     *
     * @return array
     */
    public function getModpacks(): array
    {
        return $this->data['modpacks'];
    }

    /**
     * Returns the installed modpack
     *
     * @return mixed
     */
    public function getInstalledModpack()
    {
        $modpacks = $this->getModpacks();
        return $modpacks[$this->getGame()] ?? null;
    }

    /**
     * Returns the slot amount of the gameserver
     *
     * @return int
     */
    public function getSlots(): int
    {
        return (int)$this->data['slots'];
    }

    /**
     * Returns the mysql credentials of the gameserver
     *
     * @return array
     */
    public function getMySQLCredentials(): array
    {
        if (!isset($this->data['credentials']['mysql']) &&
            empty($this->data['credentials']['mysql'])) {
            return [];
        }

        return [
            'hostname' => $this->data['credentials']['mysql']['hostname'],
            'port' => $this->data['credentials']['mysql']['port'],
            'username' => $this->data['credentials']['mysql']['username'],
            'password' => $this->data['credentials']['mysql']['password'],
            'database' => $this->data['credentials']['mysql']['database'],
        ];
    }

    /**
     * Returns the ftp credentials of the gameserver
     *
     * @return array
     */
    public function getFTPCredentials(): array
    {
        if (!isset($this->data['credentials']['ftp']) &&
            empty($this->data['credentials']['ftp'])) {
            return [];
        }

        return [
            'hostname' => $this->data['credentials']['ftp']['hostname'],
            'port' => $this->data['credentials']['ftp']['port'],
            'username' => $this->data['credentials']['ftp']['username'],
            'password' => $this->data['credentials']['ftp']['password'],
        ];
    }

    /**
     * Returns the query informations
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->data['query'];
    }

    /**
     * Returns the memory level
     *
     * @return string
     */
    public function getMemory(): string
    {
        return (string)$this->data['memory'];
    }

    /**
     * Returns the memory in mb
     *
     * @return int
     */
    public function getMemoryInMB(): int
    {
        return (int)$this->data['memory_mb'];
    }

    /**
     * Returns the gameserver type
     *
     * @return string
     */
    public function getType(): string
    {
        return (string)$this->data['type'];
    }

    /**
     * Returns the label short
     *
     * @return string
     */
    public function getLabel(): string
    {
        return (string)$this->data['label'];
    }

    /**
     * Returns the user id
     *
     * @return int
     */
    public function getUserId(): int
    {
        return (int)$this->data['user_id'];
    }

    /**
     * Returns the service id
     *
     * @return int
     */
    public function getServiceId(): int
    {
        return (int)$this->data['service_id'];
    }

    /**
     * Gets the linux hostystem
     *
     * @return array
     */
    public function getLinuxHostsystem(): array
    {
        return $this->data['hostsystems']['linux'];
    }

    /**
     * Gets the windows hostsystem
     *
     * @return array
     */
    public function getWindowsHostsystem(): array
    {
        return $this->data['hostsystems']['windows'] ?? [];
    }

    /**
     * Returns the customer settings
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->data['settings'];
    }

    /**
     * Returns the quota limit info
     *
     * @return mixed
     */
    public function getQuota()
    {
        return $this->data['quota'];
    }

    /**
     * Returns the websocket token
     *
     * @return string
     */
    public function getWebsocketToken(): string
    {
        return $this->data['websocket_token'];
    }

    /**
     * Returns the gameserver game path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->data['game_specific']['path'];
    }

    /**
     * Return gameserver game path status
     *
     * @return bool
     */
    public function isPathAvailable(): bool
    {
        return $this->data['game_specific']['path_available'];
    }

    /**
     * Returns the log files of the server
     *
     * @return array
     */
    public function getLogFiles(): array
    {
        return $this->data['game_specific']['log_files'];
    }

    /**
     * Returns the config files of the server
     *
     * @return array
     */
    public function getConfigFiles(): array
    {
        return $this->data['game_specific']['config_files'];
    }

    /**
     * Returns the last update status
     *
     * @return string
     */
    public function getUpdateStatus(): string
    {
        return $this->data['game_specific']['update_status'];
    }

    /**
     * Returns the last update status
     *
     * @return DateTime|null
     */
    public function getLastUpdate(): ?DateTime
    {
        if (empty($this->data['game_specific']['last_update'])) {
            return null;
        }

        $dateTime = new DateTime();
        $dateTime->setTimestamp(strtotime($this->data['game_specific']['last_update']));
        return $dateTime;
    }

    /**
     * Returns all features available for the service
     *
     * @return array
     */
    public function getFeatures(): array
    {
        return $this->data['game_specific']['features'];
    }

    /**
     * Returns the customer settings
     *
     * @return array
     */
    public function getCurseforgeCustomerSettings(): array
    {
        return $this->data['game_specific']['curseforge_customer_settings'];
    }

    /**
     * Returns an associative array of limits for Curseforge mods
     *
     * The key determines the type of limit. Only active limits are present.
     *
     * Possible types:
     *   'per-category': Associative array of category IDs to maximum recommended number of mods in the category
     *
     * @return array
     */
    public function getCurseforgeLimits(): array
    {
        return $this->data['game_specific']['curseforge_limits'];
    }

    /**
     * Returns the maximum allowed file size of mods in MiB
     *
     * @return int
     */
    public function getModQuotaMegabytes(): int
    {
        return $this->data['game_specific']['mod_quota_mb'];
    }
}
