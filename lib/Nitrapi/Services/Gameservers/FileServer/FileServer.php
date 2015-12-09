<?php

namespace Nitrapi\Services\Gameservers\FileServer;

use GuzzleHttp\Exception\RequestException;
use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Services\Gameservers\Gameserver;

class FileServer
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    protected $lastError = null;

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }

    /**
     * Returns the upload token and url. You can post the file by your own directly to the url.
     *
     * @param $path
     * @param $name
     * @return array
     * @throws NitrapiErrorException
     * @throws \Nitrapi\Common\Exceptions\NitrapiHttpErrorException
     */
    public function uploadToken($path, $name) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/upload";
        $upload = $this->service->getApi()->dataPost($url, array(
            'path' => $path,
            'file' => $name
        ));

        $token = $upload['token'];
        if (empty($token['token']) || empty($token['url'])) {
            throw new NitrapiErrorException('Unknown error while getting upload token');
        }

        return $token;
    }

    /**
     * Uploads a specific file
     *
     * @param $file
     * @param $path
     * @param $name
     * @return bool
     * @throws NitrapiErrorException
     */
    public function uploadFile($file, $path, $name) {
        if (!file_exists($file) || !is_readable($file)) {
            throw new NitrapiErrorException('Can\'t find local file');
        }

        $upload = $this->uploadToken($path, $name);

        try {
            $this->service->getApi()->post($upload['url'], array(
                'headers' => array(
                    'content-type' => 'application/binary',
                    'token' => $upload['token']
                ),
                'body' => Stream::factory(fopen($file, 'rb')),
            ));
        } catch (RequestException $e) {
            var_dump($e->getResponse()->getBody()->getContents());
            $response = $e->getResponse()->json();
            throw new NitrapiErrorException($response['message']);
        }

        return true;
    }

    /**
     * Writes a specific file. File will be overwritten if it's already existing.
     *
     * @param $path
     * @param $name
     * @param $content
     * @return bool
     * @throws NitrapiErrorException
     * @throws \Nitrapi\Common\Exceptions\NitrapiHttpErrorException
     */
    public function writeFile($path, $name, $content) {
        if (empty($content)) {
            throw new NitrapiErrorException('Not content provided.');
        }
        $upload = $this->uploadToken($path, $name);

        try {
            $this->service->getApi()->dataPost($upload['url'], null, null, array(
                'body' => $content,
                'headers' => array(
                    'content-type' => 'application/binary',
                    'token' => $upload['token']
                )
            ));
        } catch (RequestException $e) {
            $response = $e->getResponse()->json();
            throw new NitrapiErrorException($response['message']);
        }

        return true;
    }

    /**
     * Lists all files and folder inside of a given directory
     *
     * @param $dir
     * @return array
     * @throws \Nitrapi\Common\Exceptions\NitrapiHttpErrorException
     */
    public function getFileList($dir) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/list";

        $entries = $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'dir' => $dir
            ]
        ]);

        return $entries['entries'];
    }

    /**
     * Searches inside a specific directory recursively for specific file pattern.
     *
     * @param $dir
     * @param $search
     * @return array
     * @throws \Nitrapi\Common\Exceptions\NitrapiHttpErrorException
     */
    public function doFileSearch($dir, $search) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/list";

        $entries = $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'dir' => $dir,
                'search' => $search
            ]
        ]);

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
        $download = $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'file' => $file
            ]
        ]);

        $token = $download['token'];
        if (empty($token['token']) || empty($token['url'])) {
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

        if (file_exists($path . DIRECTORY_SEPARATOR . $name)) {
            throw new NitrapiErrorException('The target file '.$path . DIRECTORY_SEPARATOR . $name.' already exists');
        }

        $download = $this->downloadToken($file);

        $resource = fopen($path . DIRECTORY_SEPARATOR . $name, 'wb');
        $stream = \GuzzleHttp\Psr7\stream_for($resource);

        $this->service->getApi()->dataGet($download['token']['url'], null, [
            'sink' => $stream
        ]);
        return true;
    }

    /**
     * Reads a specific file
     *
     * @param $file
     * @return string
     */
    public function readFile($file) {
        $download = $this->downloadToken($file);

        // Here we use the GuzzleClient API directly. This is intended, but
        // should remain a special case. Don't copy this code.
        $request = $this->service->getApi()->createRequest('GET', $download['token']['url']);
        $response = $this->service->getApi()->send($request);

        return $response->getBody()->getContents();
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
     * Gets the file size of the given path
     *
     * @param $path
     * @return int
     */
    public function pathSize($path) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/size";
        $result = $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'path' => $path
            ]
        ]);

        return (int)$result['size'];
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

    /**
     * Moves a file to another directory
     *
     * @param $sourceFile
     * @param $targetDir
     * @param $fileName
     * @return bool
     */
    public function moveFile($sourceFile, $targetDir, $fileName) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/move";
        $this->service->getApi()->dataPost($url, array(
            'source_path' => $sourceFile,
            'target_path' => $targetDir,
            'target_filename' => $fileName
        ));
        return true;
    }

    /**
     * Moves a directory to another directory (recursive)
     *
     * @param $source
     * @param $target
     * @return bool
     */
    public function moveDirectory($source, $target) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/move";
        $this->service->getApi()->dataPost($url, array(
            'source_path' => $source,
            'target_path' => $target
        ));
        return true;
    }

    /**
     * Copies a file to another directory
     *
     * @param $source
     * @param $targetDir
     * @param $fileName
     * @return bool
     */
    public function copyFile($source, $targetDir, $fileName) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/copy";
        $this->service->getApi()->dataPost($url, array(
            'source_path' => $source,
            'target_path' => $targetDir,
            'target_name' => $fileName
        ));
        return true;
    }


    /**
     * Copies a directory to another directory (recursive)
     *
     * @param $source
     * @param $targetDir
     * @param $dirName
     * @return bool
     */
    public function copyDirectory($source, $targetDir, $dirName) {
        return $this->copyFile($source, $targetDir, $dirName);
    }

    /**
     * Creates a new directory
     *
     * @param $path
     * @param $name
     * @return bool
     */
    public function createDirectory($path, $name) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/mkdir";
        $this->service->getApi()->dataPost($url, array(
            'path' => $path,
            'name' => $name
        ));
        return true;
    }
}