<?php

namespace App\Libraries\GSuite\Services\Drive;

use App\Libraries\GSuite\GSuiteClass;
use App\Libraries\Utils\Utils;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Drive_DriveFile;
use Exception;
use Illuminate\Support\Facades\Cache;

class Drive
{

    /**
     * Mime-type of directory in Google Drive
     */
    const DIRECTORY_MIME_TYPE = 'application/vnd.google-apps.folder';

    /**
     * Root directory alias.
     * Always must be root
     */
    const ROOT_DIRECTORY = 'root';

    /**
     * Path for user's files
     */
    const PATH_TYPE_USERS = 0;

    /**
     * Prefix for cache key
     */
    const CACHED_DIRECTORY_PREFIX = 'google-drive-cached-directory';

    /**
     * Prefix for key which is used for avoid racing condition
     */
    const CACHED_DELAY_PREFIX = 'delay';

    /**
     * Virtual directory separator
     */
    const DIRECTORY_SEPARATOR = '÷';

    /**
     * Directory ID delimiter. Example: Australia I20I
     */
    const DIRECTORY_ID_DELIMITER = 'I';

    const URL = 'https://drive.google.com/uc?export=view&id=';

    /**
     * Google Drive client
     * @var \Google_Client
     */
    public $client;

    /**
     * Google Drive service
     * @var Google_Service_Drive
     */
    public $service;

    public function __construct( GSuiteClass $client )
    {
        $this->client = $client;
        $this->service = new Google_Service_Drive( $client );
    }

    public function copy($targetID, $directoryID)
    {
        $copied = new Google_Service_Drive_DriveFile();
        $copied->setParents([$directoryID]);

        return $this->service->files->copy($this->parseID($targetID), $copied, [
            'fields' => '*'
        ]);
    }

    /**
     * Deletes files or directories based on given $urls (url or ID)
     * @param array $urls
     */
    public function delete(array $urls)
    {
        foreach ($urls as $url) {

            $id = $this->parseID($url);

            if (!$id) {
                continue;
            }

            try {
                $this->service->files->delete($id);
            } catch (Exception $e) {
                // omitted
            }
        }
    }

    /**
     * Creates a new directory if not exists
     * @param string $path
     * @param bool $cached True if we want to use directoryID from cache
     * @return string Directory ID
     */
    public function touchDirectory($path, $cached = true)
    {
        // avoidance of racing condition
        $delayCacheKey = $this->setDelay($path);

        $currentDirectoryID = self::ROOT_DIRECTORY;

        foreach (explode(self::DIRECTORY_SEPARATOR, $path) as $dirName) {
            // set md5 of partial path
            $cacheKey = self::CACHED_DIRECTORY_PREFIX . '-' . md5($currentDirectoryID . '/' . $dirName);

            if ($cached) {
                $cachedDirectoryID = Cache::get($cacheKey);

                if ($cachedDirectoryID) {
                    $currentDirectoryID = $cachedDirectoryID;

                    continue;
                }
            }

            $searchString = 'mimeType="' . self::DIRECTORY_MIME_TYPE . '" and "' . $currentDirectoryID . '" in parents  and trashed = false';

            // Dir name contains label and ID
            if (preg_match('/^(.+) ' . self::DIRECTORY_ID_DELIMITER . '([0-9]+)' . self::DIRECTORY_ID_DELIMITER . '$/u', $dirName, $matches)) {
                $searchString = 'name contains "' . self::DIRECTORY_ID_DELIMITER . $matches[2] . self::DIRECTORY_ID_DELIMITER . '" and ' . $searchString;
            } else {
                $searchString = 'name="' . $dirName . '" and ' . $searchString;
            }

            $list = $this->service->files->listFiles([
                'q' => $searchString
            ])->getFiles();

            // create a new dir
            if (!count($list)) {
                $directoryMetadata = new Google_Service_Drive_DriveFile([
                    'name' => count($matches) ? $matches[0] : $dirName,
                    'mimeType' => self::DIRECTORY_MIME_TYPE,
                    'parents' => [$currentDirectoryID]
                ]);

                $directory = $this->service->files->create($directoryMetadata);

                $this->share($directory->id, [
                    'type' => 'anyone',
                    'role' => 'reader'
                ]);

                $currentDirectoryID = $directory->id;
            } else {
                // if we need rename dir title + id
                if (count($matches) && $list[0]->getName() != $matches[0]) {
                    $this->update($list[0]->getId(), [
                        'name' => $matches[0]
                    ]);
                }

                $currentDirectoryID = $list[0]->getId();
            }

            if ($cached) {
                Cache::put($cacheKey, $currentDirectoryID, 60);
            }
        }

        self::removeDelay($delayCacheKey);

        return $currentDirectoryID;
    }

    /**
     * Set delay if doesn't exist or wait
     * @param string $path Directory path
     * @return string cache key name
     */
    private function setDelay($path)
    {
        $repository = config('gsuite.drive_root_directory');

        // we will store in cache prefix of current path.
        // if this prefix already in cache - then we will wait for first process.
        // the first process will remove prefix from cache, after that the second process will continue working
        $pathPrefix = '';

        if (preg_match('/^' . $repository . self::DIRECTORY_SEPARATOR . 'Users' . self::DIRECTORY_SEPARATOR . '.+ ' . self::DIRECTORY_ID_DELIMITER . '[0-9]+' . self::DIRECTORY_ID_DELIMITER . '/ui', $path, $matches)) {
            $pathPrefix = $matches[0];
        }

        if (preg_match('/^' . $repository . self::DIRECTORY_SEPARATOR . 'Properties' . self::DIRECTORY_SEPARATOR . '.+ ' . self::DIRECTORY_ID_DELIMITER . '[0-9]+' . self::DIRECTORY_ID_DELIMITER . '/ui', $path, $matches)) {
            $pathPrefix = $matches[0];
        }

        if (preg_match('/^' . $repository . self::DIRECTORY_SEPARATOR . 'Other/ui', $path, $matches)) {
            $pathPrefix = $matches[0];
        }

        $keyValue = self::CACHED_DELAY_PREFIX . '-' . md5($pathPrefix ? $pathPrefix : $path);

        $tries = 30;

        for ($i = 1; $i <= $tries; $i++) {
            if (Cache::store('file')->has($keyValue)) {
                sleep(1);
            } else {
                Cache::store('file')->put($keyValue, true, 10);

                break;
            }
        }

        return $keyValue;
    }

    /**
     * Remove earlier set delay
     * @param string $keyValue cache value
     */
    private function removeDelay($keyValue)
    {
        Cache::store('file')->forget($keyValue);
    }

    /**
     * Shares file or directory
     * @param string $id File or directory ID (url http://...)
     * @param array $permissionParams
     * @param array $optParams
     */
    public function share($id, array $permissionParams, $optParams = [])
    {
        // sometimes share() throws an error.
        // that's why we are trying to use share() several times
        $tries = 10;

        for ($i = 1; $i <= $tries; $i++) {
            try {
                $permission = new Google_Service_Drive_Permission($permissionParams);

                $this->service->permissions->create($this->parseID($id), $permission, $optParams);

                return;
            } catch (Exception $e) {
                sleep(3);
            }
        }
    }

    public function parseID($url)
    {
        if (preg_match('/id=([a-z0-9-_]+)/ui', $url, $matches)) {
            return $matches[1];
        }

        return $url;
    }

    /**
     * Updates file|directory properties
     * @param string $id File|Directory ID or url
     * @param array $params
     */
    public function update($id, array $params)
    {
        $file = new Google_Service_Drive_DriveFile();

        if (isset($params['name'])) {
            $file->setName($params['name']);

            unset($params['name']);
        }

        $this->service->files->update($this->parseID($id), $file, $params);
    }

    /**
     * Returns array of Google_Service_Drive_DriveFile files
     * @param string $directoryID
     * @param array $fields
     * @param bool $recursively Search in child directories
     * @return array
     */
    public function directoryFiles($directoryID, $fields = [], $recursively = false)
    {
        $array = [];
        $pageToken = null;

        $parameters = [
            'q' => '"' . $directoryID . '" in parents and trashed = false',
            'orderBy' => 'folder',
            'pageSize' => 1000,
        ];

        if (count($fields)) {
            $parameters['fields'] = "files(". join(', ', $fields) .")";
        }

        do {
//            try {
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }

                $files = $this->service->files->listFiles($parameters);

                foreach ($files->getFiles() as $file) {
                    // search in child directory(es)
                    if ($file->getMimeType() == self::DIRECTORY_MIME_TYPE && $recursively) {
                        foreach ($this->directoryFiles($file->getId(), $fields, $recursively) as $nestedFile) {
                            $array[] = $nestedFile;
                        }
                    } else {
                        $array[] = $file;
                    }
                }

                $pageToken = $files->getNextPageToken();
//            } catch (Exception $e) {
//                $pageToken = null;
//            }
        } while ($pageToken);

        return $array;
    }

    /**
     * Return path to Google Drive directory.
     * We need to change method setDelay if it changes
     * @param integer $type
     * @param array $params
     * @return string
     */
    public function path($type, $params = [])
    {
        $prefix = config('services.google.drive_root_directory') . self::DIRECTORY_SEPARATOR;

        switch ($type) {
            case self::PATH_TYPE_USERS:
                $user = empty($params['user']) ? Utils::getCurrentUser() : $params['user'];
                return $prefix
                    . 'users'
                    . self::DIRECTORY_SEPARATOR
                    . (isset($params['user_id']) ? $params['user_id'] : $user->id);
        }
    }

    /**
     * Creates a new file and new directory if needed
     *
     * @param string $filePath
     * @param string $fileName
     * @param string $content
     *
     * @return Google_Service_Drive_DriveFile Created file
     */
    public function put($filePath, $fileName, $content)
    {
        $directoryID = self::touchDirectory($filePath);

        $file = new Google_Service_Drive_DriveFile();
        $file->setName($fileName);
        $file->setParents([$directoryID]);

        return $this->service->files->create($file, [
            'data' => $content,
            'fields' => '*'
        ]);
    }

    /**
     * Returns link to file by given ID
     * @param string $id
     * @return string
     */
    public function createURL($id)
    {
        return self::URL . $id;
    }

    public function get($id)
    {
        try {
            return $this->service->files->get($id, [
                'fields' => '*'
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Checks for file/directory existing
     * @param string $path
     * @param bool $cached
     * @return boolean|string
     */
    public function exists($path, $cached = true)
    {
        $currentDirectoryID = self::ROOT_DIRECTORY;

        foreach (explode(self::DIRECTORY_SEPARATOR, $path) as $dirName) {
            // set md5 of partial path
            $cacheKey = self::CACHED_DIRECTORY_PREFIX . '-' . md5($currentDirectoryID . '/' . $dirName);

            if ($cached) {
                $cachedDirectoryID = Cache::get($cacheKey);

                if ($cachedDirectoryID) {
                    $currentDirectoryID = $cachedDirectoryID;

                    continue;
                }
            }

            $searchString = '"' . $currentDirectoryID . '" in parents and trashed = false';

            $searchedDirectory = null;

            // Dir name contains label and ID
            if (preg_match('/^(.+) ' . self::DIRECTORY_ID_DELIMITER . '([0-9]+)' . self::DIRECTORY_ID_DELIMITER . '$/u', $dirName, $matches)) {
                $files = $this->directoryFiles($currentDirectoryID);

                foreach ($files as $file) {
                    if (!preg_match('/^(.+) ' . self::DIRECTORY_ID_DELIMITER . $matches[2] . self::DIRECTORY_ID_DELIMITER . '$/u', $file->getName())) {
                        continue;
                    }

                    $searchedDirectory = $file;
                }
            } else {
                $searchString = 'name="' . $dirName . '" and ' . $searchString;

                $files = $this->service->files->listFiles([
                    'q' => $searchString
                ])->getFiles();

                $searchedDirectory = count($files) ? $files[0] : $searchedDirectory;
            }

            if (!$searchedDirectory) {
                return false;
            }

            $currentDirectoryID = $searchedDirectory->getId();

            if ($cached) {
                Cache::put($cacheKey, $currentDirectoryID, 60);
            }
        }

        return $currentDirectoryID;
    }

}
