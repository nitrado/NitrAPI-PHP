<?php

namespace Nitrapi\Services\Voiceservers\Types;

class Teamspeak3 extends Type
{
    public function status($show_icons = false) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/status";
        $params = [];
        if($show_icons) {
            $params["show_icons"] = true;
        }
        $status = $this->service->getApi()->dataGet($url, null, ["query" => $params])['status'];
        return $status;
    }
    
    public function icon($icon_id) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/icon";
        $icon = $this->service->getApi()->dataGet($url, null, ["query" => ["icon_id" => $icon_id]])['icon'];
        return $icon;
    }
    
    public function getWhitelist() {
        $url = "services/" . $this->service->getId() . "/voiceservers/whitelist";
        return $this->service->getApi()->dataGet($url, null)['list'];
    }
    
    public function addWhitelist($ip, $comment) {
        $url = "services/" . $this->service->getId() . "/voiceservers/whitelist";
        return $this->service->getApi()->dataPost($url, [
            'ip' => $ip,
            'comment' => $comment
        ])['entry'];
    }
    
    public function deleteWhitelist($id) {
        $url = "services/" . $this->service->getId() . "/voiceservers/whitelist";
        $this->service->getApi()->dataDelete($url, [
            'id' => $id
        ]);
        return true;
    }

    public function getBanlist() {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/bans";
        return $this->service->getApi()->dataGet($url, null);
    }

    public function deleteBanlist($id) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/bans";
        $this->service->getApi()->dataDelete($url, [
            'ban_id' => $id
        ]);
        return true;
    }

    public function setHostMessage($mode, $message) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/hostmessage";
        $this->service->getApi()->dataPost($url, [
            'mode' => $mode,
            'message' => $message,
        ]);
        return true;
    }

    public function addPassword($password, $description, $duration = 10) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/temppassword";
        $this->service->getApi()->dataPost($url, [
            'password' => $password,
            'description' => $description,
            'duration' => $duration,
        ]);
        return true;
    }

    public function enableLogView($group) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/enable_log_view";
        return $this->service->getApi()->dataPost($url, [
            'group' => $group
        ]);
    }
    
    public function cleanupUsers($groups, $days) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/cleanup_users";
        return $this->service->getApi()->dataPost($url, [
            'groups' => $groups,
            'days' => $days
        ])['cleanup'];
    }
    
    public function info() {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/info";
        return $this->service->getApi()->dataGet($url, null)['info'];
    }
    
    /**
     * Send commands to voiceserver
     * 
     * @param array $commands
     * @return array
     */
    public function query($commands) {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/query";
        $response = $this->service->getApi()->dataPost($url, null, null, [
            'json' => [
                'commands' => $commands
            ]
        ]);
        return $response['query'];
    }
    
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