<?php


namespace App\Repositories\Enums;


use Illuminate\Support\Carbon;
use Jiannei\Enum\Laravel\Repositories\Enums\CacheEnum as BaseCacheEnum;

class CacheEnum extends BaseCacheEnum
{
    // 表明+业务描述
    public const LINKS_SIDEBAR = 'linksSidebar';
    public const USERS_ACTIVE = 'usersActive';

    protected static function linksSidebar($options)
    {
        return Carbon::now()->addDays();
    }

    protected static function usersActive($options)
    {
        return Carbon::now()->addHours();
    }
}
