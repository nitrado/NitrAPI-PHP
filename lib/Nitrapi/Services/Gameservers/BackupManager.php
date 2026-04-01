<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Common\Exceptions\NitrapiException;

class BackupManager
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver $service)
    {
        $this->service = $service;
    }


    /**
     * Returns details about the backup archive
     *
     * @return array|bool|string
     * @throws NitrapiException
     */
    public function info()
    {
        $url = 'services/' . $this->service->getId() . '/backups/info';
        $response = $this->service->getApi()->dataGet($url);

        if (!isset($response)) {
            throw new NitrapiErrorException('Unable to fetch repository details');
        }

        return $response;
    }

    /**
     * List archives available in the repository
     *
     * @return array
     * @throws NitrapiException
     */
    public function list(): array
    {
        $url = 'services/' . $this->service->getId() . '/backups';
        $response = $this->service->getApi()->dataGet($url);

        if (!isset($response)) {
            throw new NitrapiErrorException('No backup have been returned from API.');
        }

        return $response;
    }

    /**
     * Create a backup
     *
     * @param string $type type of backup
     * @return bool
     * @throws NitrapiException
     */
    public function create(string $type = 'game'): bool
    {
        $url = 'services/' . $this->service->getId() . '/backups';
        $response = $this->service->getApi()->dataPost($url, [
            'type' => $type,
        ]);

        if (!isset($response) || $response['status'] !== 'success') {
            throw new NitrapiErrorException('Backup creation failed!');
        }

        return $response;
    }


    /**
     * Restore a backup
     *
     * @param string $backup name of the archive
     * @param array $paths
     * @return bool
     * @throws NitrapiException
     */
    public function extract(string $backup, array $paths = []): bool
    {
        $url = 'services/' . $this->service->getId() . '/backups/extract';
        $response = $this->service->getApi()->dataPost($url, [
            'name' => $backup,
            'paths' => $paths,
        ]);

        if (!isset($response)) {
            throw new NitrapiErrorException('Backup restore failed!');
        }

        return $response;
    }

    /**
     * Deletes an archive
     *
     * @param string $backup name of the archive
     * @return bool
     * @throws NitrapiException
     */
    public function delete(string $backup): bool
    {
        $url = 'services/' . $this->service->getId() . '/backups';
        $response = $this->service->getApi()->dataDelete($url, [], [], [
            'query' => [
                'prefix' => $backup,
            ],
        ]);

        if (!isset($response)) {
            throw new NitrapiErrorException('Backup deletion failed!');
        }

        return $response;
    }
}
