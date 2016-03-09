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
}