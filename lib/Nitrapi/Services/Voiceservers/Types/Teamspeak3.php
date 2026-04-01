<?php

namespace Nitrapi\Services\Voiceservers\Types;

use Nitrapi\Common\Exceptions\NitrapiException;

class Teamspeak3 extends Type
{
    /**
     * @throws NitrapiException
     */
    public function status($show_icons = false)
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/status";
        $params = [];
        if ($show_icons) {
            $params["show_icons"] = true;
        }
        return $this->service->getApi()->dataGet($url, null, ["query" => $params])['status'];
    }

    /**
     * @throws NitrapiException
     */
    public function icon($icon_id)
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/icon";
        return $this->service->getApi()->dataGet($url, null, ["query" => ["icon_id" => $icon_id]])['icon'];
    }

    /**
     * @throws NitrapiException
     */
    public function getWhitelist()
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/whitelist";
        return $this->service->getApi()->dataGet($url, null)['list'];
    }

    /**
     * @throws NitrapiException
     */
    public function addWhitelist($ip, $comment)
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/whitelist";
        return $this->service->getApi()->dataPost($url, [
            'ip' => $ip,
            'comment' => $comment,
        ])['entry'];
    }

    /**
     * @throws NitrapiException
     */
    public function deleteWhitelist($id): bool
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/whitelist";
        $this->service->getApi()->dataDelete($url, [
            'id' => $id,
        ]);
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function getBanlist()
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/bans";
        return $this->service->getApi()->dataGet($url, null);
    }

    /**
     * @throws NitrapiException
     */
    public function deleteBanlist($id): bool
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/bans";
        $this->service->getApi()->dataDelete($url, [
            'ban_id' => $id,
        ]);
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function setHostMessage($mode, $message): bool
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/hostmessage";
        $this->service->getApi()->dataPost($url, [
            'mode' => $mode,
            'message' => $message,
        ]);
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function addPassword($password, $description, $duration = 10): bool
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/temppassword";
        $this->service->getApi()->dataPost($url, [
            'password' => $password,
            'description' => $description,
            'duration' => $duration,
        ]);
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function enableLogView($group)
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/enable_log_view";
        return $this->service->getApi()->dataPost($url, [
            'group' => $group,
        ]);
    }

    /**
     * @throws NitrapiException
     */
    public function cleanupUsers($groups, $days)
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/cleanup_users";
        return $this->service->getApi()->dataPost($url, [
            'groups' => $groups,
            'days' => $days,
        ])['cleanup'];
    }

    /**
     * @throws NitrapiException
     */
    public function info()
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/info";
        return $this->service->getApi()->dataGet($url, null)['info'];
    }

    /**
     * Send commands to voiceserver
     *
     * @param array $commands
     * @return array
     * @throws NitrapiException
     */
    public function query(array $commands): array
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/query";
        $response = $this->service->getApi()->dataPost($url, null, null, [
            'json' => [
                'commands' => $commands,
            ],
        ]);
        return $response['query'];
    }

    /**
     * Creates a new Admin Group
     *
     * @throws NitrapiException
     */
    public function addGroup($name): string
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/group";
        return $this->service->getApi()->dataPost($url, [
            'name' => $name,
        ])['token'];
    }

    /**
     * Deletes a Group
     *
     * @throws NitrapiException
     */
    public function deleteGroup($groupId): bool
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/group";
        $this->service->getApi()->dataDelete($url, [
            'sgid' => $groupId,
        ]);

        return true;
    }

    /**
     * Adds a new token for a Group
     *
     * @throws NitrapiException
     */
    public function addToken($groupId): string
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/token";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'sgid' => $groupId,
            ],
        ])['token'];
    }

    /**
     * Deletes a Token
     *
     * @return true
     * @throws NitrapiException
     */
    public function deleteToken($token): bool
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/token";
        $this->service->getApi()->dataDelete($url, [
            'token' => $token,
        ]);

        return true;
    }

    /**
     * Shows all available teamspeak 3 hostsystems
     *
     * @admin
     * @return array
     * @throws NitrapiException
     */
    public function getHostsystems(): array
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/servers";
        return $this->service->getApi()->dataGet($url)['servers'];
    }

    /**
     * Switches a teamspeak 3 instance to a new hostsystem
     * Except files and icons, these are not migrated
     *
     * @admin
     * @return true
     * @throws NitrapiException
     */
    public function doSwitch($hostname): bool
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/teamspeak3/switch";
        $this->service->getApi()->dataPost($url, [
            'server' => $hostname,
        ]);

        return true;
    }
}
