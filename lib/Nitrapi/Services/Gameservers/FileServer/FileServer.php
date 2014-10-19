<?php

namespace Nitrapi\Services\Gameservers\FileServer;

use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Services\Gameservers\Gameserver;

class FileServer
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    protected $lastError = null;

    public function __construct(Gameserver $service) {
        $this->service = $service;
    }

    public function getFileList($dir) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/list";

        $entries = $this->service->getApi()->dataGet($url . '?dir=' . $dir);

        return $entries['entries'];
    }

    /**
     * Returns the download token and url for a file
     *
     * @param $file
     * @return array
     * @throws \Nitrapi\Common\Exceptions\NitrapiErrorException
     */
    public function downloadToken($file) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/download";
        $download = $this->service->getApi()->dataGet($url . '?file=' . $file);

        if (empty($download['token']) || empty($download['url'])) {
            throw new NitrapiErrorException('Unknown error while downloading');
        }

        return $download;
    }

    /**
     * Downloads a file and safes it directly to a specified path
     *
     * @param $file
     * @param $path
     * @param $name
     * @return bool
     * @throws \Nitrapi\Common\Exceptions\NitrapiErrorException
     */
    public function downloadFile($file, $path, $name) {
        if (!is_writeable($path)) {
            throw new NitrapiErrorException('The target directory "' . $path . '" is not writeable');
        }

        $download = $this->downloadToken($file);
        $url = $download['url'];
        $this->service->getApi()->get($url)
            ->setResponseBody($path . DIRECTORY_SEPARATOR . $name)
            ->send();
        return true;
    }

    /**
     * Deletes a file from server
     *
     * @param $file
     * @return bool
     */
    public function deleteFile($file) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/delete";
        $this->service->getApi()->dataDelete($url, array(
            'path' => $file
        ));

        return true;
    }

    /**
     * Deletes a directory with content from server
     *
     * @param $directory
     * @return bool
     */
    public function deleteDirectory($directory) {
        return $this->deleteFile($directory);
    }
}