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
                'body' => \GuzzleHttp\Psr7\stream_for(fopen($file, 'rb')),
            ));
        } catch (RequestException $e) {
            $response = $e->getResponse()->json();
            throw new NitrapiErrorException($response['message']);
        }

        return true;
    }

    /**
     * Returns a list with Bookmarks for easier Navigation.
     *
     * @return array
     */
    public function getBookmarks() {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/bookmarks";

        $entries = $this->service->getApi()->dataGet($url);

        return $entries['bookmarks'];
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
     * Returns the seek token and url for a file
     *
     * @param $file
     * @return array
     * @throws \Nitrapi\Common\Exceptions\NitrapiErrorException
     */
    public function seekToken($file, $offset, $length, $mode) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/seek";
        $seek = $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'file' => $file,
                'offset' => $offset,
                'length' => $length,
                'mode' => $mode
            ]
        ]);

        $token = $seek['token'];
        if (empty($token['token']) || empty($token['url'])) {
            throw new NitrapiErrorException('Unknown error while getting download token');
        }

        return $seek;
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

        $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => [
                'token' => $download['token']['token']
            ],
            'sink' => $stream
        ]);
        return true;
    }

    /**
     * Reads a part from a specific file
     *
     * @param $file
     * @param $offset
     * @param $count
     * @return string
     */
    public function readPartFromFile($file, $offset = 0, $count = null) {
        $download = $this->downloadToken($file);

        // Here we use the GuzzleClient API directly. This is intended, but
        // should remain a special case. Don't copy this code.
        $response = $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => [
                'token' => $download['token']['token'],
                'offset' => $offset,
                'count' => $count,
            ]
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * Reads a specific file from the gameserver
     *
     * Read the content from a filepath, living on the gameserver host. You
     * can access all files from your gameserver root directory which you
     * have sufficient permissions for. To limit the amount of data you will
     * receive from the host, you can set the $maxKB variable for that. This
     * is especially useful, if you don't know the size of the file. The default
     * is 100MB. If that limit is exceeded, an Exception is thrown. So always
     * wrap that method call in a try-catch to make your code bullet proof.
     *
     * @param string $file remote filepath
     * @param int $maxKB max size of the file.
     * @return string requested file data
     * @throws NitrapiErrorException if the requested file is too big
     */
    public function readFile($file, $maxKB=102400) {
        $download = $this->downloadToken($file);

        // Here we use the GuzzleClient API directly. This is intended, but
        // should remain a special case. Don't copy this code.
        $response = $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => [
                'token' => $download['token']['token']
            ]
        ]);

        // Because PHP can't handle infinite amount of data in one request
        // (PHP puts the data in memory), we read chunks of the file until
        // we reach a byte limit. If we hit that limit, we throw an error,
        // otherwise the data is returned.
        $body = $response->getBody();
        $bytesRead = 0;
        $data = '';

        while (!$body->eof()) {
            $chunk = $body->read(1024);
            $data .= $chunk;
            $bytesRead += strlen($chunk);

            if ($bytesRead >= $maxKB*1024) {
                $body->close();
                throw new NitrapiErrorException('File is too big.');
            }
        }

        return $data;
    }

    /**
     * Seek a specific
     *
     * @param $file
     * @param $offset
     * @param $length
     * @param $mode [raw|lines]
     * @return string
     */
    public function seekFile($file, $offset, $length = 4048, $mode = 'raw') {
        $download = $this->seekToken($file, $offset, $length, $mode);

        // Here we use the GuzzleClient API directly. This is intended, but
        // should remain a special case. Don't copy this code.
        $response = $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => [
                'token' => $download['token']['token']
            ]
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * Reads x bytes from file head
     *
     * @param $file
     * @param length
     * @return string
     */
    public function headFile($file, $length) {
        return $this->seekFile($file, 0, $length);
    }

    /**
     * Reads x bytes from file tail
     *
     * @param $file
     * @param length
     * @return string
     */
    public function tailFile($file, $length) {
        return $this->seekFile($file, -($length), $length);
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
     * Returns stat infos by file array
     *
     * @param $files
     * @return array
     */
    public function statFiles(array $files) {
        $url = "/services/".$this->service->getId()."/gameservers/file_server/stat";

        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'files' => $files
            ]
        ])['entries'];
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