<?php


namespace App\Repositories\Enums;


use Jiannei\Enum\Laravel\Repositories\Enums\CacheEnum as BaseCacheEnum;

class CacheEnum extends BaseCacheEnum
{
    public const LINKS_SIDEBAR = 'linksSidebar';

    protected static function linksSidebar($options)
    {
        return 1440 * 60;
    }
}
