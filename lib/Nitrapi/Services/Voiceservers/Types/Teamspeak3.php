<?php

namespace Nitrapi\Services\Voiceservers\Types;

class Teamspeak3 extends Type
{
    /**
     * Creates a new Admin Group
     *
     * @return string
     */
    public function addGroup($name) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/group";
        return $this->service->getApi()->dataPost($url, [
            'name' => $name
        ])['token'];
    }

    /**
     * Deletes a Group
     *
     * @return string
     */
    public function deleteGroup($groupId) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/group";
        $this->service->getApi()->dataDelete($url, [
            'sgid' => $groupId
        ]);

        return true;
    }

    /**
     * Adds a new token for a Group
     *
     * @return string
     */
    public function addToken($groupId) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/token";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'sgid' => $groupId
            ]
        ])['token'];
    }

    /**
     * Deletes a Token
     *
     * @return string
     */
    public function deleteToken($token) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/token";
        $this->service->getApi()->dataDelete($url, [
            'token' => $token
        ]);

        return true;
    }

    /**
     * Shows all available teamspeak 3 hostsystems
     *
     * @admin
     * @return array
     */
    public function getHostsystems() {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/servers";
        return $this->service->getApi()->dataGet($url)['servers'];
    }

    /**
     * Switches a teamspeak 3 instance to a new hostsystem
     * Except files and icons, these are not migrated
     *
     * @admin
     * @return bool
     */
    public function doSwitch($hostname) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/switch";
        $this->service->getApi()->dataPost($url, [
            'server' => $hostname
        ]);

        return true;
    }

}