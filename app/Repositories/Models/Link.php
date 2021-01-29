<?php

namespace App\Repositories\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Link extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'link'];

    public $cache_key = 'larabbs_links';
    protected $cache_expire_in_seconds = 1440 * 60;

    /**
     * 兼容 Laravel 8 的 Factory.
     *
     * @return LinkFactory
     */
    protected static function newFactory()
    {
        return LinkFactory::new();
    }

    public function getAllCached()
    {
        // 尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        // 否则运行匿名函数中的代码来取出 links 表中所有的数据，返回的同时做了缓存。
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function () {
            return $this->all();
        });
    }
}
