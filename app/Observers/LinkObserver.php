<?php

namespace App\Observers;

use App\Repositories\Enums\CacheEnum;
use App\Repositories\Models\Link;
use Illuminate\Support\Facades\Cache;

class LinkObserver
{
    public function saved(Link $link)
    {
        Cache::forget(CacheEnum::getCacheKey(CacheEnum::LINKS_SIDEBAR));
    }
}
