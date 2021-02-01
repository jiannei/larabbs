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
use App\Support\Traits\Services\UploadImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class UserService
{
    use UploadImage;

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

    public function calculateActiveUsers()
    {
        $topicWeight = 4;// 话题权重 TODO 算法
        $replyWeight = 1;// 回复权重
        $passDays = 7;
        $userNumber = 6; // 取出来多少用户

        $topicUsers = $this->topicRepository->queryPastUsers($passDays);

        // 根据话题数量计算得分
        $topicScores = [];
        $topicUsers->each(function ($item) use (&$topicScores, $topicWeight) {
            $topicScores[$item->user_id] = [
                'score' => $item->topic_count * $topicWeight,// TODO
            ];
        });

        $replyUsers = $this->replyRepository->queryPastUsers($passDays);

        // 根据回复数量计算得分
        $replyScores = [];
        $replyUsers->each(function ($item) use (&$replyScores, $replyWeight) {
            $replyScores[$item->user_id] = [
                'score' => $item->reply_count * $replyWeight,// TODO
            ];
        });

        $userIds = array_unique(array_merge(array_keys($topicScores), array_keys($replyScores)));
        // 汇总得分
        $scores = [];
        foreach ($userIds as $userId) {
            $scores[$userId]['score'] = $topicScores[$userId]['score'] ?? 0;

            if (isset($replyScores[$userId])) {
                $scores[$userId]['score'] += $replyScores[$userId]['score'];
            }
        }

        // 数组按照得分排序
        $scores = Arr::sort($scores, function ($user) {
            return $user['score'];
        });

        $scores = array_reverse($scores, true);
        $scores = array_slice($scores, 0, $userNumber, true);

        $activeUsers = collect();
        foreach ($scores as $user_id => $user) {
            $user = $this->userRepository->find($user_id);
            if ($user) {
                $activeUsers->push($user);
            }
        }

        return $activeUsers;
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
