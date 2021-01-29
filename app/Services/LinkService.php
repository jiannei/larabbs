<?php


namespace App\Services;


use App\Contracts\Repositories\LinkRepository;
use App\Repositories\Eloquent\LinkRepositoryEloquent;
use App\Repositories\Enums\CacheEnum;
use Illuminate\Support\Facades\Cache;

class LinkService
{
    /**
     * @var LinkRepositoryEloquent
     */
    private $repository;

    public function __construct(LinkRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleSearchAll()
    {
        $cacheKey = CacheEnum::getCacheKey(CacheEnum::LINKS_SIDEBAR);
        $cacheExpireTime = CacheEnum::getCacheExpireTime(CacheEnum::LINKS_SIDEBAR);

        return Cache::remember($cacheKey, $cacheExpireTime, function () {
            return $this->repository->all();
        });
    }
}
