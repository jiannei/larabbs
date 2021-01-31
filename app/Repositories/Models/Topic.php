<?php

namespace App\Repositories\Models;

use Database\Factories\TopicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'body', 'category_id', 'excerpt', 'slug', 'user_id'
    ];

    /**
     * 兼容 Laravel 8 的 Factory.
     *
     * @return TopicFactory
     */
    protected static function newFactory()
    {
        return TopicFactory::new();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function link($params = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }

    public function updateReplyCount()
    {
        $this->reply_count = $this->replies->count();
        $this->save();
    }
}
