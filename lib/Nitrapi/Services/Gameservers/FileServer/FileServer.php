<?php

namespace Nitrapi\Services\Gameservers\FileServer;

use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\Gameservers\Gameserver;

class FileServer
{
    /** @var Gameserver */
    protected $service;

    public function __construct(Gameserver $service)
    {
        $this->service = $service;
    }

    /**
     * Returns the upload token and url. You can post the file directly to the url yourself.
     *
     * @param string $path
     * @param string $name
     *
     * @return array
     *
     * @throws NitrapiException
     */
    public function uploadToken(string $path, string $name): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/upload";
        $upload = $this->service->getApi()->dataPost($url, [
            'path' => $path,
            'file' => $name,
        ]);

        $token = $upload['token'];
        if (empty($token['token']) || empty($token['url'])) {
            throw new NitrapiErrorException('Unknown error while getting upload token');
        }

        return $token;
    }

    /**
     * Uploads a local file to the game server.
     *
     * @param string $file
     * @param string $path
     * @param string $name
     * @return bool
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
     *
     * @throws NitrapiException
     */
    public function getBookmarks(): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/bookmarks";
        return $this->service->getApi()->dataGet($url)['bookmarks'];
    }

    /**
     * Writes a file. The file is overwritten if it already exists.
     *
     * @throws NitrapiException
     */
    public function writeFile(string $path, string $name, string $content): bool
    {
        $upload = $this->uploadToken($path, $name);
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
    public function getFileList(string $dir, bool $summarizeFolders = false): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/list";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'dir' => $dir,
                'summarize_folders' => $summarizeFolders ? 1 : 0,
            ],
        ])['entries'];
    }

    /**
     * Recursively searches a directory for files matching a pattern.
     *
     * @throws NitrapiException
     */
    public function doFileSearch(string $dir, string $search): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/list";
        return $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'dir' => $dir,
                'search' => $search,
            ],
        ])['entries'];
    }

    /**
     * Returns the seek token and url for a file.
     *
     * @throws NitrapiException
     */
    public function seekToken(string $file, int $offset, int $length, string $mode): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/seek";
        $seek = $this->service->getApi()->dataGet($url, null, [
            'query' => [
                'file' => $file,
                'offset' => $offset,
                'length' => $length,
                'mode' => $mode,
            ],
        ]);

        $token = $seek['token'];
        if (empty($token['token']) || empty($token['url'])) {
            throw new NitrapiErrorException('Unknown error while getting seek token');
        }

        return $seek;
    }

    /**
     * Returns the download token and url for a file.
     *
     * @throws NitrapiException
     */
    public function downloadToken(string $file): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/download";
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
     * Reads a remote file, up to $maxKB kilobytes.
     *
     * @throws NitrapiErrorException if the file exceeds the size limit
     * @throws NitrapiException
     */
    public function readFile(string $file, int $maxKB = 102400): string
    {
        $download = $this->downloadToken($file);
        $response = $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => ['token' => $download['token']['token']],
        ]);

        $body = $response->getBody();
        $bytesRead = 0;
        $data = '';

        while (!$body->eof()) {
            $chunk = $body->read(8192);
            $data .= $chunk;
            $bytesRead += strlen($chunk);

            if ($bytesRead >= $maxKB * 1024) {
                $body->close();
                throw new NitrapiErrorException('File is too big.');
            }
        }

        return $data;
    }

    /**
     * Reads a range of bytes from a remote file.
     *
     * @param string $file
     * @param int $offset
     * @param int $length
     * @param string $mode [raw|lines]
     * @return string
     * @throws NitrapiException
     */
    public function seekFile(string $file, int $offset, int $length = 4048, string $mode = 'raw'): string
    {
        $download = $this->seekToken($file, $offset, $length, $mode);
        $response = $this->service->getApi()->request('GET', $download['token']['url'], [
            'query' => ['token' => $download['token']['token']],
        ]);

        return (string)$response->getBody();
    }

    /**
     * Reads the first $length bytes of a remote file.
     * @throws NitrapiException
     */
    public function headFile(string $file, int $length): string
    {
        return $this->seekFile($file, 0, $length);
    }

    /**
     * Reads the last $length bytes of a remote file.
     *
     * @throws NitrapiException
     */
    public function tailFile(string $file, int $length): string
    {
        return $this->seekFile($file, -$length, $length);
    }

    /**
     * Deletes a file from the server.
     * @throws NitrapiException
     */
    public function deleteFile(string $file): bool
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/delete";
        $this->service->getApi()->dataDelete($url, ['path' => $file]);
        return true;
    }

    /**
     * Returns stat info for an array of file paths.
     * @throws NitrapiException
     */
    public function statFiles(array $files): array
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/stat";
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
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/size";
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
    public function moveFile(string $sourceFile, string $targetDir, string $fileName): bool
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/move";
        $this->service->getApi()->dataPost($url, [
            'source_path' => $sourceFile,
            'target_path' => $targetDir,
            'target_filename' => $fileName,
        ]);
        return true;
    }

    /**
     * Moves a directory recursively.
     * @throws NitrapiException
     */
    public function moveDirectory(string $source, string $target): bool
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/move";
        $this->service->getApi()->dataPost($url, [
            'source_path' => $source,
            'target_path' => $target,
        ]);
        return true;
    }

    /**
     * Copies a file to another directory.
     * @throws NitrapiException
     */
    public function copyFile(string $source, string $targetDir, string $fileName): bool
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/copy";
        $this->service->getApi()->dataPost($url, [
            'source_path' => $source,
            'target_path' => $targetDir,
            'target_name' => $fileName,
        ]);
        return true;
    }

    /**
     * Recursively copies a directory.
     * @throws NitrapiException
     */
    public function copyDirectory(string $source, string $targetDir, string $dirName): bool
    {
        return $this->copyFile($source, $targetDir, $dirName);
    }

    /**
     * Creates a new directory.
     * @throws NitrapiException
     */
    public function createDirectory(string $path, string $name): bool
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/file_server/mkdir";
        $this->service->getApi()->dataPost($url, [
            'path' => $path,
            'name' => $name,
        ]);
        return true;
    }
}
