<?php

namespace Nitrapi\Services\CloudServers\FileServer;

use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\CloudServers\CloudServer;

class FileServer
{
    /** @var CloudServer */
    protected $service;

    public function __construct(CloudServer $service)
    {
        $this->service = $service;
    }

    /**
     * Returns the upload token and url. You can post the file directly to the url yourself.
     *
     * @throws NitrapiException
     */
    public function uploadToken(string $path, string $name, ?string $username = null): array
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/upload";
        $upload = $this->service->getApi()->dataPost($url, [
            'path' => $path,
            'file' => $name,
            'username' => $username,
        ]);

        $token = $upload['token'];
        if (empty($token['token']) || empty($token['url'])) {
            throw new NitrapiErrorException('Unknown error while getting upload token');
        }

        return $token;
    }

    /**
     * Uploads a local file to the cloud server.
     *
     * @throws NitrapiException
     */
    public function uploadFile(string $file, string $path, string $name): bool
    {
        if (!file_exists($file) || !is_readable($file)) {
            throw new NitrapiErrorException('Can\'t find local file');
        }

        $upload = $this->uploadToken($path, $name);
        $api = $this->service->getApi();

        $stream = $api->getStreamFactory()->createStreamFromResource(fopen($file, 'rb'));

        $api->request('POST', $upload['url'], [
            'headers' => [
                'Content-Type' => 'application/binary',
                'token' => $upload['token'],
            ],
            'body' => $stream,
        ]);

        return true;
    }

    /**
     * Returns a list of bookmarks for easier navigation.
     * @throws NitrapiException
     */
    public function getBookmarks(): array
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/bookmarks";
        return $this->service->getApi()->dataGet($url)['bookmarks'];
    }

    /**
     * Writes a file. The file is overwritten if it already exists.
     *
     * @throws NitrapiException
     */
    public function writeFile(string $path, string $name, string $content, ?string $username = null): bool
    {
        $upload = $this->uploadToken($path, $name, $username);
        $api = $this->service->getApi();

        $api->request('POST', $upload['url'], [
            'headers' => [
                'Content-Type' => 'application/binary',
                'token' => $upload['token'],
            ],
            'body' => $content,
        ]);

        return true;
    }

    /**
     * Lists all files and folders inside a given directory.
     *
     * @throws NitrapiException
     */
    public function getFileList(?string $dir = null): array
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/list";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => ['dir' => $dir],
        ])['entries'];
    }

    /**
     * Recursively searches a directory for files matching a pattern.
     *
     * @throws NitrapiException
     */
    public function doFileSearch(string $dir, string $search): array
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/list";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'dir' => $dir,
                'search' => $search,
            ],
        ])['entries'];
    }

    /**
     * Returns the download token and url for a file.
     *
     * @throws NitrapiException
     */
    public function downloadToken(string $file): array
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/download";
        $download = $this->service->getApi()->dataGet($url, null, [
            'query' => ['file' => $file],
        ]);

        $token = $download['token'];
        if (empty($token['token']) || empty($token['url'])) {
            throw new NitrapiErrorException('Unknown error while getting download token');
        }

        return $download;
    }

    /**
     * Downloads a remote file and saves it to the local filesystem.
     *
     * @throws NitrapiException
     */
    public function downloadFile(string $file, string $path, string $name): bool
    {
        if (!is_writable($path)) {
            throw new NitrapiErrorException('The target directory "' . $path . '" is not writeable');
        }

        $targetPath = $path . DIRECTORY_SEPARATOR . $name;
        if (file_exists($targetPath)) {
            throw new NitrapiErrorException('The target file ' . $targetPath . ' already exists');
        }

        $download = $this->downloadToken($file);
        $response = $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => ['token' => $download['token']['token']],
        ]);

        file_put_contents($targetPath, (string)$response->getBody());

        return true;
    }

    /**
     * Reads a portion of a remote file.
     * @throws NitrapiException
     */
    public function readPartFromFile(string $file, int $offset = 0, ?int $count = null): string
    {
        $download = $this->downloadToken($file);
        $response = $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => [
                'token' => $download['token']['token'],
                'offset' => $offset,
                'count' => $count,
            ],
        ]);

        return (string)$response->getBody();
    }

    /**
     * Reads a remote file fully.
     * @throws NitrapiException
     */
    public function readFile(string $file): string
    {
        $download = $this->downloadToken($file);
        $response = $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => ['token' => $download['token']['token']],
        ]);

        return (string)$response->getBody();
    }

    /**
     * Deletes a file from the server.
     * @throws NitrapiException
     */
    public function deleteFile(string $file): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/delete";
        $this->service->getApi()->dataDelete($url, ['path' => $file]);
        return true;
    }

    /**
     * Returns stat info for an array of file paths.
     * @throws NitrapiException
     */
    public function statFiles(array $files): array
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/stat";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => ['files' => $files],
        ])['entries'];
    }

    /**
     * Returns the disk usage of a path.
     * @throws NitrapiException
     */
    public function pathSize(string $path): int
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/size";
        return (int)$this->service->getApi()->dataGet($url, null, [
            'query' => ['path' => $path],
        ])['size'];
    }

    /**
     * Recursively deletes a directory.
     * @throws NitrapiException
     */
    public function deleteDirectory(string $directory): bool
    {
        return $this->deleteFile($directory);
    }

    /**
     * Moves a file to another directory.
     * @throws NitrapiException
     */
    public function moveFile(string $sourceFile, string $targetDir, string $fileName, ?string $username = null): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/move";
        $this->service->getApi()->dataPost($url, [
            'source_path' => $sourceFile,
            'target_path' => $targetDir,
            'target_filename' => $fileName,
            'username' => $username,
        ]);
        return true;
    }

    /**
     * Moves a directory recursively.
     * @throws NitrapiException
     */
    public function moveDirectory(string $source, string $target, ?string $username = null): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/move";
        $this->service->getApi()->dataPost($url, [
            'source_path' => $source,
            'target_path' => $target,
            'username' => $username,
        ]);
        return true;
    }

    /**
     * Copies a file to another directory.
     * @throws NitrapiException
     */
    public function copyFile(string $source, string $targetDir, string $fileName, ?string $username = null): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/copy";
        $this->service->getApi()->dataPost($url, [
            'source_path' => $source,
            'target_path' => $targetDir,
            'target_name' => $fileName,
            'username' => $username,
        ]);
        return true;
    }

    /**
     * Recursively copies a directory.
     * @throws NitrapiException
     */
    public function copyDirectory(string $source, string $targetDir, string $dirName, ?string $username = null): bool
    {
        return $this->copyFile($source, $targetDir, $dirName, $username);
    }

    /**
     * Creates a new directory.
     * @throws NitrapiException
     */
    public function createDirectory(string $path, string $name, ?string $username = null): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/mkdir";
        $this->service->getApi()->dataPost($url, [
            'path' => $path,
            'name' => $name,
            'username' => $username,
        ]);
        return true;
    }

    /**
     * Changes the ownership of a path.
     * @throws NitrapiException
     */
    public function chown(string $path, string $username, string $group, bool $recursive = false): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/chown";
        $this->service->getApi()->dataPost($url, [
            'path' => $path,
            'username' => $username,
            'group' => $group,
            'recursive' => $recursive ? 'true' : 'false',
        ]);
        return true;
    }

    /**
     * Changes the permissions of a path.
     * @throws NitrapiException
     */
    public function chmod(string $path, string $chmod, bool $recursive = false): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/file_server/chmod";
        $this->service->getApi()->dataPost($url, [
            'path' => $path,
            'chmod' => $chmod,
            'recursive' => $recursive ? 'true' : 'false',
        ]);
        return true;
    }
}
