<?php

namespace SotonJitsu\Twitter\Cache;

use SotonJitsu\Exception\CannotLoadFile;

class File implements Cache
{
    private $cacheContents = null;

    private $filePath;

    public function __construct($filePath)
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new CannotLoadFile('Cache file at ' . $filePath . ' could not be loaded');
        }

        $this->filePath = $filePath;
    }

    public function has($cacheKey)
    {
        $this->readCache();

        // TODO Invalidation on the entire cache is currently sufficient, but obviously this SHOULD be per item
        $lastModifiedTime = filemtime($this->filePath);
        return isset($this->cacheContents->$cacheKey) && ((time() - $lastModifiedTime) < 3600);
    }

    public function get($cacheKey)
    {
        $this->readCache();

        return $this->cacheContents->$cacheKey;
    }

    public function set($cacheKey, $value)
    {
        $this->readCache();

        $this->cacheContents->$cacheKey = is_string($value) ? $value : json_encode($value);
    }

    /**
     * Lazy-load the cache
     */
    private function readCache()
    {
        if (is_null($this->cacheContents)) {
            $raw = json_decode(file_get_contents($this->filePath));

            if (is_null($raw)) {
                $raw = new \stdClass();
            }

            $this->cacheContents = $raw;
        }
    }

    /**
     * Save the cache back to the file
     */
    public function save()
    {
        $this->readCache();

        file_put_contents($this->filePath, json_encode($this->cacheContents));
    }
}
