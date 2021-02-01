<?php

namespace App\Repositories\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Link extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'link'];

    /**
     * 兼容 Laravel 8 的 Factory.
     *
     * @return LinkFactory
     */
    protected static function newFactory()
    {
        return LinkFactory::new();
    }
}
