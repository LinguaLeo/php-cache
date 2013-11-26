<?php

namespace LinguaLeo\Cache\Provider;

use LinguaLeo\Cache\CacheInterface;

abstract class CacheProvider implements CacheInterface
{

    protected static $prefix = "cache";
    protected static $delimiter = ":";

    /**
     * @param string $prefix
     */
    public static function setPrefix($prefix)
    {
        self::$prefix = $prefix;
    }

    /**
     * @param string $delimiter
     */
    public static function setDelimiter($delimiter)
    {
        self::$delimiter = $delimiter;
    }

    /**
     * Generate cache key.
     * Optionally you can pass a class name as the first parameter and if
     * this class will contain constant named "VERSION" than it will be mixed up
     * with the result key.
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function generateCacheKey()
    {
        $args = func_get_args();
        if (empty($args)) {
            throw new \InvalidArgumentException('At least one argument must be passed to generate cache key.');
        }
        $const = $args[0].'::VERSION';
        $version = defined($const) ? self::$delimiter . constant($const) : '';
        return self::$prefix . self::$delimiter . implode(self::$delimiter, $args) . $version;
    }

}