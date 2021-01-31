<?php

namespace App\Repositories\Models;

use Database\Factories\ReplyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        'content', 'user_id', 'topic_id'
    ];

    /**
     * 兼容 Laravel 8 的 Factory.
     *
     * @return ReplyFactory
     */
    protected static function newFactory()
    {
        return ReplyFactory::new();
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
