<?php

namespace App\Traits;

class LazyLoadingFlag
{
    protected static bool $used = false;

    public static function markUsed(): void
    {
        static::$used = true;
    }

    public static function wasUsed(): bool
    {
        return static::$used;
    }

    public static function reset(): void
    {
        static::$used = false;
    }
}
