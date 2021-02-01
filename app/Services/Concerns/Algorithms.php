<?php


namespace App\Services\Concerns;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait Algorithms
{
    protected $topicWeight = 4;// 话题权重
    protected $replyWeight = 1;// 回复权重
    protected $passDays = 7;// 计算过去 7 天
    protected $limit = 6;// 计算 6 条

    /**
     * 计算活跃用户
     *
     * @return Collection
     */
    public function calculateActiveUsers(): Collection
    {
        $topicWeight = $this->topicWeight;
        $replyWeight = $this->replyWeight;

        $topicUsers = $this->topicRepository->queryPastUsers($this->passDays);

        // 根据话题数量计算得分
        $topicScores = [];
        $topicUsers->each(function ($item) use (&$topicScores, $topicWeight) {
            $topicScores[$item->user_id] = $item->topic_count * $topicWeight;// TODO 公式提取
        });

        $replyUsers = $this->replyRepository->queryPastUsers($this->passDays);

        // 根据回复数量计算得分
        $replyScores = [];
        $replyUsers->each(function ($item) use (&$replyScores, $replyWeight) {
            $replyScores[$item->user_id] = $item->reply_count * $replyWeight;// TODO
        });

        $userIds = array_unique(array_merge(array_keys($topicScores), array_keys($replyScores)));
        // 汇总得分
        $scores = [];
        foreach ($userIds as $userId) {
            $scores[$userId] = $topicScores[$userId] ?? 0;

            if (isset($replyScores[$userId])) {
                $scores[$userId] += $replyScores[$userId];
            }
        }

        // 数组按照得分排序（倒序）
        $scores = array_reverse(Arr::sort($scores), true);
        $scores = array_slice($scores, 0, $this->limit, true);

        $activeUsers = collect();
        foreach ($scores as $user_id => $score) {
            $user = $this->userRepository->find($user_id);
            if ($user) {
                $activeUsers->push($user);
            }
        }

        return $activeUsers;
    }
}
