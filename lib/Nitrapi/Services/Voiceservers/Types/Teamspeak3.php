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
}