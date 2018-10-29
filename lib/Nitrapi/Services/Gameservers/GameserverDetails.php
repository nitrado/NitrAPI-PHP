<?php

namespace Nitrapi\Services\Gameservers;


class GameserverDetails
{
    protected $data;

    public function __construct(array &$data) {
        $this->data = $data;
    }

    /**
     * Returns the current gameserver status
     *
     * @return string
     */
    public function getStatus() {
        return (string)$this->data['status'];
    }

    /**
     * @return bool
     */
    public function isManagedRoot() {
        return isset($this->data['managed_root']);
    }

    /**
     * @return array
     */
    public function getManagedRoot() {
        if (!$this->isManagedRoot()) return [];
        return $this->data['managed_root'];
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUsername() {
        return (string)$this->data['username'];
    }

    /**
     * Returns the gameserver ip address
     *
     * @return string
     */
    public function getIP() {
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
    public function getIPv6() {
        return $this->data['ipv6'];
    }

    /**
     * Returns the gameserver port
     *
     * @return int
     */
    public function getPort() {
        return (int)$this->data['port'];
    }

    /**
     * Returns the gameserver query port
     *
     * @return int
     */
    public function getQueryPort() {
        return (int)$this->data['query_port'];
    }

    /**
     * Returns the gameserver rcon port
     *
     * @return int
     */
    public function getRconPort() {
        return (int)$this->data['rcon_port'];
    }

    /**
     * Returns true if the gameserver is in minecraft mode
     *
     * @return bool
     */
    public function isMinecraftMode() {
        return (bool)$this->data['minecraft_mode'];
    }

    /**
     * Returns the game
     *
     * @return string
     */
    public function getGame() {
        return (string)$this->data['game'];
    }

    /**
     * Returns the installed modpacks
     *
     * @return string
     */
    public function getModpacks() {
        return $this->data['modpacks'];
    }

    /**
     * Returns the installed modpack
     *
     * @return mixed
     */
    public function getInstalledModpack() {
        $modpacks = $this->getModpacks();
        if (isset($modpacks[$this->getGame()])) {
            return $modpacks[$this->getGame()];
        }
        return null;
    }

    /**
     * Returns the slot amount of the gameserver
     *
     * @return int
     */
    public function getSlots() {
        return (int)$this->data['slots'];
    }

    /**
     * Returns the mysql credentials of the gameserver
     *
     * @return array
     */
    public function getMySQLCredentials() {
        if (!isset($this->data['credentials']['mysql']) &&
            empty($this->data['credentials']['mysql'])) {
            return array();
        }

        return array(
            'hostname' => $this->data['credentials']['mysql']['hostname'],
            'port' => $this->data['credentials']['mysql']['port'],
            'username' => $this->data['credentials']['mysql']['username'],
            'password' => $this->data['credentials']['mysql']['password'],
            'database' => $this->data['credentials']['mysql']['database'],
        );
    }

    /**
     * Returns the ftp credentials of the gameserver
     *
     * @return array
     */
    public function getFTPCredentials() {
        if (!isset($this->data['credentials']['ftp']) &&
            empty($this->data['credentials']['ftp'])) {
            return array();
        }

        return array(
            'hostname' => $this->data['credentials']['ftp']['hostname'],
            'port' => $this->data['credentials']['ftp']['port'],
            'username' => $this->data['credentials']['ftp']['username'],
            'password' => $this->data['credentials']['ftp']['password'],
        );
    }

    /**
     * Returns the query informations
     *
     * @return array
     */
    public function getQuery() {
        return $this->data['query'];
    }

    /**
     * Returns the memory level
     *
     * @return string
     */
    public function getMemory() {
        return (string)$this->data['memory'];
    }

    /**
     * Returns the memory in mb
     *
     * @return int
     */
    public function getMemoryInMB() {
        return (int)$this->data['memory_mb'];
    }

    /**
     * Returns the gameserver type
     *
     * @return string
     */
    public function getType() {
        return (string)$this->data['type'];
    }

    /**
     * Returns the label short
     *
     * @return string
     */
    public function getLabel() {
        return (string)$this->data['label'];
    }

    /**
     * Returns the user id
     *
     * @return string
     */
    public function getUserId() {
        return (int)$this->data['user_id'];
    }

    /**
     * Returns the service id
     *
     * @return string
     */
    public function getServiceId() {
        return (int)$this->data['service_id'];
    }

    /**
     * Gets the linux hostystem
     *
     * @return array
     */
    public function getLinuxHostsystem() {
        return $this->data['hostsystems']['linux'];
    }

    /**
     * Gets the windows hostsystem
     *
     * @return array
     */
    public function getWindowsHostsystem() {
        if (isset($this->data['hostsystems']['windows'])) {
            return $this->data['hostsystems']['windows'];
        }
        return array();
    }

    /**
     * Returns the customer settings
     *
     * @return array
     */
    public function getSettings() {
        return $this->data['settings'];
    }

    /**
     * Returns the quota limit info
     *
     * @return mixed
     */
    public function getQuota() {
        return $this->data['quota'];
    }

    /**
     * Returns the websocket token
     *
     * @return string
     */
    public function getWebsocketToken() {
        return $this->data['websocket_token'];
    }

    /**
     * Returns the gameserver game path
     *
     * @return string
     */
    public function getPath() {
        return $this->data['game_specific']['path'];
    }
    
    /**
     * Return gameserver game path status
     * 
     * @return boolean
     */
    public function isPathAvailable() {
        return $this->data['game_specific']['path_available'];
    }

    /**
     * Returns the log files of the server
     *
     * @return array
     */
    public function getLogFiles() {
        return $this->data['game_specific']['log_files'];
    }

    /**
     * Returns the config files of the server
     *
     * @return array
     */
    public function getConfigFiles() {
        return $this->data['game_specific']['config_files'];
    }

    /**
     * Returns the last update status
     *
     * @return string
     */
    public function getUpdateStatus() {
        return $this->data['game_specific']['update_status'];
    }

    /**
     * Returns the last update status
     *
     * @return \DateTime
     */
    public function getLastUpdate() {
        if (empty($this->data['game_specific']['last_update'])) {
            return null;
        }

        $dateTime = new \DateTime();
        $dateTime->setTimestamp(strtotime($this->data['game_specific']['last_update']));
        return $dateTime;
    }
}