<?php

namespace SotonJitsu\Twitter\Cache;

interface Cache
{
    /**
     * @param string $cacheKey
     * @return boolean
     */
    public function has($cacheKey);

    /**
     * @param string $cacheKey
     * @return mixed|null
     */
    public function get($cacheKey);

    /**
     * @param string $cacheKey
     * @param mixed $value
     * @return void
     */
    public function set($cacheKey, $value);

    /**
     * @return void
     */
    public function save();
}
