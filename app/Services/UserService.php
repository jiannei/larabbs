<?php


namespace App\Services;


use App\Contracts\Repositories\ReplyRepository;
use App\Contracts\Repositories\TopicRepository;
use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\ReplyRepositoryEloquent;
use App\Repositories\Eloquent\TopicRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use App\Repositories\Enums\CacheEnum;
use App\Repositories\Enums\RedisEnum;
use App\Repositories\Models\User;
use App\Services\Concerns\Algorithms;
use App\Services\Concerns\UploadImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class UserService
{
    use UploadImage, Algorithms;

    /**
     * @var UserRepositoryEloquent
     */
    private $userRepository;
    /**
     * @var TopicRepositoryEloquent
     */
    private $topicRepository;
    /**
     * @var ReplyRepositoryEloquent
     */
    private $replyRepository;

    public function __construct(UserRepository $userRepository, TopicRepository $topicRepository, ReplyRepository $replyRepository)
    {
        $this->userRepository = $userRepository;
        $this->topicRepository = $topicRepository;
        $this->replyRepository = $replyRepository;
    }

    public function handleSearchItem($id)
    {
        return $this->userRepository->find($id);
    }

    public function handleUpdateItem(Request $request, $id)
    {
        $attributes = $request->all();

        if ($request->avatar) {
            $result = $this->upload($request->avatar, 'avatars', $id, 416);
            if ($result) {
                $attributes['avatar'] = $result['path'];
            }
        }

        return $this->userRepository->update($attributes, $id);
    }

    public function handleActiveUsers($refresh = false)
    {
        $cacheKey = CacheEnum::getCacheKey(CacheEnum::USERS_ACTIVE);
        $cacheExpireTime = CacheEnum::getCacheExpireTime(CacheEnum::USERS_ACTIVE);

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $cacheExpireTime, function () {
            return $this->calculateActiveUsers();
        });
    }

    public function handleRecordActiveTime(User $user): bool
    {
        // 获取今日 Redis 哈希表名称，如：larabbs_last_active_at_2017-10-21
        $hashTable = RedisEnum::getHashTable(RedisEnum::LAST_ACTIVATED_AT, Carbon::today()->toDateString());

        // 字段名称，如：user_1
        $hashField = RedisEnum::getHashField(RedisEnum::LAST_ACTIVATED_AT, $user->getAttribute('id'));

        // 当前时间，如：2017-10-21 08:35:15
        $now = Carbon::now()->toDateTimeString();

        // 数据写入 Redis ，字段已存在会被更新
        Redis::hSet($hashTable, $hashField, $now);

        return true;
    }

    public function handleSyncActiveTime(): void
    {
        // 获取昨日的哈希表名称，如：larabbs_last_active_at_2017-10-21
        $hashTable = RedisEnum::getHashTable(RedisEnum::LAST_ACTIVATED_AT, Carbon::today()->toDateString());

        // 从 Redis 中获取所有哈希表里的数据
        $hashData = Redis::hGetAll($hashTable);

        // 遍历，并同步到数据库中
        foreach ($hashData as $field => $actived_at) {
            // 会将 `user_1` 转换为 1
            $user_id = last(RedisEnum::parseField($field));

            if ($user = $this->userRepository->find($user_id)) {
                $user->last_active_at = $actived_at;
                $user->save();
            }
        }

        // 以数据库为中心的存储，既已同步，即可删除
        Redis::del($hashTable);
    }
}
