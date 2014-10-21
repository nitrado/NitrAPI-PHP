<?php

namespace Nitrapi\Services\Gameservers\FileServer;

use Guzzle\Http\Exception\ServerErrorResponseException;
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

    public function uploadToken($path, $name) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/upload";
        $upload = $this->service->getApi()->dataPost($url, array(
            'path' => $path,
            'file' => $name
        ));

        if (empty($upload['token']) || empty($upload['url'])) {
            throw new NitrapiErrorException('Unknown error while getting upload token');
        }

        return $upload;
    }

    public function uploadFile($file, $path, $name) {
        if (!file_exists($file) || !is_readable($file)) {
            throw new NitrapiErrorException('Can\' find local file');
        }

        $upload = $this->uploadToken($path, $name);

        try {
            $request = $this->service->getApi()->post($upload['url'], array(
                'content-type' => 'application/binary',
                'token' => $upload['token']
            ));
            $request->setBody(fopen($file, 'rb'));
            $request->send();
        } catch (ServerErrorResponseException $e) {
            var_dump($e->getResponse()->getBody(true));
            $response = $e->getResponse()->json();
            throw new NitrapiErrorException($response['message']);
        }

        return true;
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
            throw new NitrapiErrorException('Unknown error while getting download token');
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