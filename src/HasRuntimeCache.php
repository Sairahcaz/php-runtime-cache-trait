<?php

namespace Sairahcaz\PhpRuntimeCacheTrait;

use Exception;

trait HasRuntimeCache
{
    /**
     * specifies if the runtime cache should be applied
     *
     * @var bool
     */
    private $runtimeCacheEnabled = true;

    /**
     * add global prefix for runtimeCache member variable
     *
     * @var string
     */
    private $runtimeCacheGlobalPrefix = '';

    /**
     * the array where the cache is temporary stored during runtime
     *
     * @var array
     */
    private $runtimeCache = [];

    /**
     * @param $cacheType
     * @param callable $fallback
     * @param null $cacheKey
     * @param null $prefix
     * @return array|mixed
     * @throws Exception
     */
    private function getSafeRuntimeCache($cacheType, callable $fallback, $cacheKey = null, $prefix = null)
    {
        if (!$this->runtimeCacheEnabled || !config('app.runtime_cache_enable')) {
            return $fallback();
        }

        $currentPrefix = $prefix ?? $this->runtimeCacheGlobalPrefix;
        $currentPrefix .= '_';

        $cache = null;
        $cacheTypePrefix = $currentPrefix.$cacheType;

        if (!array_key_exists($cacheTypePrefix, $this->runtimeCache)) {
            throw new Exception("unknown runtime cache type: $cacheType, please specify it first (e.g. in class constructor)");
        }

        if (!is_null($cacheKey)) {
            if (is_null($this->runtimeCache[$cacheTypePrefix]) || !isset($this->runtimeCache[$cacheTypePrefix][$cacheKey])) {
                //if we execute the fallback, make sure we do it only once during runtime and avoid this value to be null again
                $this->runtimeCache[$cacheTypePrefix][$cacheKey] = $fallback() ?? false;
            }

            $cache = $this->runtimeCache[$cacheTypePrefix][$cacheKey];
        } else {
            if (is_null($this->runtimeCache[$cacheTypePrefix])) {
                //if we execute the fallback, make sure we do it only once during runtime and avoid this value to be null again
                $this->runtimeCache[$cacheTypePrefix] = $fallback() ?? false;
            }

            $cache = $this->runtimeCache[$cacheTypePrefix];
        }

        return $cache;
    }
}