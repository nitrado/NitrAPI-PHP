<?php

namespace Nitrapi\Services\CloudServers\FileServer;

use GuzzleHttp\Exception\RequestException;
use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Services\CloudServers\CloudServer;

class FileServer
{
    /**
     * @var CloudServer $service
     */
    protected $service;

    protected $lastError = null;

    public function __construct(CloudServer &$service) {
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
    public function uploadToken($path, $name, $username = null) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/upload";
        $upload = $this->service->getApi()->dataPost($url, array(
            'path' => $path,
            'file' => $name,
            'username' => $username
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
            var_dump($e->getResponse()->getBody()->getContents());
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
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/bookmarks";

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
    public function writeFile($path, $name, $content, $username = null) {
        $upload = $this->uploadToken($path, $name, $username);

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
    public function getFileList($dir = null) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/list";

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
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/list";

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
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/download";
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
     * Reads a specific file
     *
     * @param $file
     * @return string
     */
    public function readFile($file) {
        $download = $this->downloadToken($file);

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
     * Deletes a file from server
     *
     * @param $file
     * @return bool
     */
    public function deleteFile($file) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/delete";
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
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/stat";

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
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/size";
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
     * @param $username
     * @return bool
     */
    public function moveFile($sourceFile, $targetDir, $fileName, $username = null) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/move";
        $this->service->getApi()->dataPost($url, array(
            'source_path' => $sourceFile,
            'target_path' => $targetDir,
            'target_filename' => $fileName,
            'username' => $username,
        ));
        return true;
    }

    /**
     * Moves a directory to another directory (recursive)
     *
     * @param $source
     * @param $target
     * @param $username
     * @return bool
     */
    public function moveDirectory($source, $target, $username = null) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/move";
        $this->service->getApi()->dataPost($url, array(
            'source_path' => $source,
            'target_path' => $target,
            'username' => $username
        ));
        return true;
    }

    /**
     * Copies a file to another directory
     *
     * @param $source
     * @param $targetDir
     * @param $fileName
     * @param $username
     * @return bool
     */
    public function copyFile($source, $targetDir, $fileName, $username = null) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/copy";
        $this->service->getApi()->dataPost($url, array(
            'source_path' => $source,
            'target_path' => $targetDir,
            'target_name' => $fileName,
            'username' => $username
        ));
        return true;
    }


    /**
     * Copies a directory to another directory (recursive)
     *
     * @param $source
     * @param $targetDir
     * @param $dirName
     * @param $username
     * @return bool
     */
    public function copyDirectory($source, $targetDir, $dirName, $username = null) {
        return $this->copyFile($source, $targetDir, $dirName, $username);
    }

    /**
     * Creates a new directory
     *
     * @param $path
     * @param $name
     * @param $username
     * @return bool
     */
    public function createDirectory($path, $name, $username = null) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/mkdir";
        $this->service->getApi()->dataPost($url, array(
            'path' => $path,
            'name' => $name,
            'username' => $username
        ));
        return true;
    }

    /**
     * Chowns a specified path
     *
     * @param $path
     * @param $username
     * @param $group
     * @param $recursive
     * @return bool
     */
    public function chown($path, $username, $group, $recursive = false) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/chown";
        $this->service->getApi()->dataPost($url, array(
            'path' => $path,
            'username' => $username,
            'group' => $group,
            'recursive' => ($recursive ? 'true' : 'false')
        ));
        return true;
    }

    /**
     * Chmods a specified path
     *
     * @param $path
     * @param $chmod
     * @param $recursive
     * @return bool
     */
    public function chmod($path, $chmod, $recursive = false) {
        $url = "/services/".$this->service->getId()."/cloud_servers/file_server/chmod";
        $this->service->getApi()->dataPost($url, array(
            'path' => $path,
            'chmod' => $chmod,
            'recursive' => ($recursive ? 'true' : 'false')
        ));
        return true;
    }
}