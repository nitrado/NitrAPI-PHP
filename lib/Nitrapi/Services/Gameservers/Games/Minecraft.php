<?php

namespace Nitrapi\Services\Gameservers\Games;

use Nitrapi\Common\Exceptions\NitrapiException;

class Minecraft extends Game
{
    protected $game = "minecraft";

    /**
     * Starting the minecraft chunkfix with given world
     *
     * @param $world
     * @param int $limit
     * @return bool
     * @throws NitrapiException
     */
    public function startChunkfix($world, int $limit = 0): bool
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/chunkfix";
        $this->service->getApi()->dataPost($url, [
            'world' => $world,
            'limit' => $limit,
        ]);

        return true;
    }

    /**
     * Changing bungeecord settings
     *
     * @param bool $enabled
     * @param bool $only
     * @param string $firewall
     * @param mixed $ip
     * @return bool
     * @throws NitrapiException
     */
    public function setBungeeCord(bool $enabled = false, bool $only = false, string $firewall = 'off', $ip = null): bool
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/bungeecord";
        $this->service->getApi()->dataPost($url, [
            'enabled' => (int)$enabled,
            'only' => (int)$only,
            'firewall' => $firewall,
            'firewall_ip' => $ip,
        ]);

        return true;
    }

    /**
     * Changing rtk settings
     *
     * @param bool $enabled
     * @param string|null $username
     * @param string|null $password
     * @return bool
     * @throws NitrapiException
     */
    public function setRemoteToolkit(bool $enabled = false, ?string $username = null, ?string $password = null): bool
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/rtk";
        $this->service->getApi()->dataPost($url, [
            'enabled' => (int)$enabled,
            'username' => $username,
            'password' => $password,
        ]);

        return true;
    }

    /**
     * Creates a new backup of a specified world
     *
     * @param $world
     * @return bool
     * @throws NitrapiException
     */
    public function createBackup($world): bool
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/backup";
        $this->service->getApi()->dataPost($url, [
            'world' => $world,
        ]);

        return true;
    }

    /**
     * Deletes a specified backup
     *
     * @param $backup
     * @return bool
     * @throws NitrapiException
     */
    public function deleteBackup($backup): bool
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/backup/" . $backup;
        $this->service->getApi()->dataDelete($url);

        return true;
    }

    /**
     * Restore a specified backup
     *
     * @param $backup
     * @return bool
     * @throws NitrapiException
     */
    public function restoreBackup($backup): bool
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/backup/" . $backup . "/restore";
        $this->service->getApi()->dataPost($url);

        return true;
    }

    /**
     * Installs a specific Minecraft Version
     *
     * @param $md5
     * @return bool
     * @throws NitrapiException
     */
    public function switchVersion($md5): bool
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/change_version";
        $this->service->getApi()->dataPost($url, [
            'md5' => $md5,
        ]);

        return true;
    }

    /**
     * Returns the formated UUID id of the specific minecraft user
     *
     * @param $username
     * @return array
     * @throws NitrapiException
     */
    public function getUUID($username): array
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/uuid";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'username' => $username,
            ],
        ])['user'];
    }

    /**
     * Returns the avatar as base64 encoded content of the specific minecraft user
     * Note: case-sensitive!
     *
     * @param $username
     * @return array
     * @throws NitrapiException
     */
    public function getAvatar($username): array
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/avatar";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'username' => $username,
            ],
        ])['user'];
    }

    /**
     * Returns all installed Bukkit/Spigot Plugins at Minecraft Bukkit
     *
     * @return array
     * @throws NitrapiException
     */
    public function getPlugins(): array
    {
        $url = "services/" . $this->service->getId() . "/gameservers/games/minecraft/plugins";
        $result = $this->service->getApi()->dataGet($url);

        return $result['plugins'] ?? [];
    }
}
