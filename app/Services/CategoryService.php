<?php


namespace App\Services;


use App\Contracts\Repositories\CategoryRepository;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Repositories\Enums\CacheEnum;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    /**
     * @var CategoryRepositoryEloquent
     */
    private $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleSearchAll()
    {
        $cacheKey = CacheEnum::getCacheKey(CacheEnum::CATEGORIES);
        $cacheExpireTime = CacheEnum::getCacheExpireTime(CacheEnum::CATEGORIES);

        return Cache::remember($cacheKey, $cacheExpireTime, function () {
            return $this->repository->all();
        });
    }
}
