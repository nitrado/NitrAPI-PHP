<?php

namespace Nitrapi\Services\Gameservers;


class GameserverFeatures
{
    protected $data;

    public function __construct(array &$data) {
        $this->data = $data;
    }

    /**
     * Return true if the game can manage world backups.
     *
     * @return boolean true if the game has world backups
     */
    public function hasWorldBackups() {
        return $this->data['has_world_backups'];
    }

    public function hasBackups() {
        return $this->data['has_backups'];
    }

    public function hasPackages() {
        return $this->data['has_packages'];
    }

    /**
     * Redirect the output stream from a running service.
     *
     * This feature is deprecated and should not be used any more. We keep
     * it here for compatibility concerns, but will be removed in the
     * future.
     *
     * @deprecated
     * @return bool if the gameserver supports the old application server
     */
    public function hasApplicationServer() {
        return $this->data['has_application_server'];
    }

    /**
     * Replacement feature for the deprecated application server.
     *
     * The container websockets allows to access a websocket server on the
     * hostsystem on 'wss://[hostsystem].gamedata.io:34882'. This websocket
     * provide the STDOUT and STDERR stream, directly from within the
     * container. It also supports events like attaching to a container or
     * a status event when the container died. you can also use this
     * websocket to send commands to a container. For that, just send a
     * JSON message with "command" and "container" keys.
     *
     * @return bool true if the gameserver supports container websockets
     */
    public function hasContainerWebsocket() {
        return $this->data['has_container_websocket'];
    }

    public function hasFileBrowser() {
        return $this->data['has_file_browser'];
    }

    public function hasExpertMode() {
        return $this->data['has_expert_mode'];
    }

    public function hasDatabase() {
        return $this->data['has_database'];
    }

    public function hasRestartMessageSupport() {
        return $this->data['has_restart_message_support'];
    }

    public function hasPlayerManagement() {
        return $this->data['has_playermanagement_feature'];
    }

    public function hasPluginSystem() {
        return $this->data['has_plugin_system'];
    }

    public function hasRCon() {
        return $this->data['has_rcon'];
    }

    public function hasFTP() {
        return $this->data['has_ftp'];
    }

    public function hasCurseforgeWorkshop() {
        return $this->data['has_curseforge_workshop'];
    }

    public function hasBlockedQueryPort() {
        return $this->data['has_hidden_queryport'];
    }

    public function hasBlockedRconPort() {
        return $this->data['has_hidden_rconport'];
    }
}
