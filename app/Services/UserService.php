<?php


namespace App\Services;


use App\Contracts\Repositories\ReplyRepository;
use App\Contracts\Repositories\TopicRepository;
use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\ReplyRepositoryEloquent;
use App\Repositories\Eloquent\TopicRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use App\Repositories\Enums\CacheEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class UserService
{
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

}
