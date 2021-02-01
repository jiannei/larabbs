<?php


namespace App\Repositories\Enums;


use Illuminate\Support\Str;
use Jiannei\Enum\Laravel\Enum;

class RedisEnum extends Enum
{
    // table => field
    public const LAST_ACTIVATED_AT = 'user';

    /**
     * Get hash table.
     *
     * @param $value
     * @param  null  $date
     * @return string
     */
    public static function getHashTable($value, $date = null): string
    {
        $table = Str::lower(static::getKey($value));

        return is_null($date) ? $table : $table.':'.$date;
    }

    /**
     * Get hash field.
     *
     * @param $value
     * @param  string|int|null  $identifier
     * @return string
     */
    public static function getHashField($value, $identifier = null): string
    {
        $field = Str::lower($value);

        return is_null($identifier) ? $field : $field.':'.$identifier;
    }

    public static function parseHashField($field)
    {
        return explode(':', $field);
    }
}
