<?php

namespace Nitrapi\Services\Gameservers;

class BackupManager
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }


    /**
     * Returns details about the backup archive
     *
     * @return object
     */
    public function info() {

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
     */
    public function list() {

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
     */
    public function create($type = 'game') {

        $url = 'services/' . $this->service->getId() . '/backups';
        $response = $this->service->getApi()->dataPost($url, [
            'type' => $type
        ]);

        if (!isset($response) || $response['status'] != 'success') {
            throw new NitrapiErrorException('Backup creation failed!');
        }

        return $response;
    }


    /**
     * Restore a backup
     *
     * @param string $backup name of the archive
     * @return bool
     */
    public function extract($backup, $paths = []) {
        $url = 'services/' . $this->service->getId() . '/backups/extract';
        $response = $this->service->getApi()->dataPost($url, [
            'name' => $backup,
            'paths' => $paths
        ]);


        if (!isset($response)) {
            throw new NitrapiErrorException('Backup restore failed!');
        }

        return $response;
    }

    /**
     * Deletes a archive
     *
     * @param string $backup name of the archive
     * @return bool
     */
    public function delete($backup) {
        $url = 'services/' . $this->service->getId() . '/backups';
        $response = $this->service->getApi()->dataDelete($url, [], [], [
            'query' => [
                'prefix' => $backup
            ]
        ]);

        if (!isset($response)) {
            throw new NitrapiErrorException('Backup deletion failed!');
        }

        return $response;
    }
}
