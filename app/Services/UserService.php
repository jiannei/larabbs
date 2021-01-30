<?php


namespace App\Services;


use App\Contracts\Repositories\ReplyRepository;
use App\Contracts\Repositories\TopicRepository;
use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\ReplyRepositoryEloquent;
use App\Repositories\Eloquent\TopicRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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

    public function handleActiveUsers(Request $request)
    {
        return $this->calculateActiveUsers();
    }

    protected function calculateActiveUsers()
    {
        $topicWeight = 4;// 话题权重 TODO 算法
        $replyWeight = 4;// 回复权重
        $passDays = 7;
        $userNumber = 6; // 取出来多少用户

        $topicUsers = $this->topicRepository->queryPastUsers($passDays);

        // 根据话题数量计算得分
        $topicScores = [];
        $topicUsers->each(function ($item) use (&$topicScores, $topicWeight) {
            $topicScores[] = [
                'score' => $item->topic_count * $topicWeight,// TODO
                'user_id' => $item->user_id,
            ];
        });

        $replyUsers = $this->replyRepository->queryPastUsers($passDays);

        // 根据回复数量计算得分
        $replyScores = [];
        $replyUsers->each(function ($item) use (&$replyScores, $replyWeight) {
            $replyScores[] = [
                'score' => $item->reply_count * $replyWeight,// TODO
                'user_id' => $item->user_id,
            ];
        });

        // 汇总得分
        $scores = [];
        foreach ($topicScores as $topic) {
            $scores[$topic['user_id']]['score'] = $topic['score'];
            foreach ($replyScores as $reply) {
                if (isset($scores[$reply['user_id']])) {
                    $scores[$reply['user_id']]['score'] += $reply['score'];
                } else {
                    $scores[$reply['user_id']]['score'] = $reply['score'];
                }
            }
        }

        // 数组按照得分排序
        $scores = Arr::sort($scores, function ($user) {
            return $user['score'];
        });

        // 我们需要的是倒序，高分靠前，第二个参数为保持数组的 KEY 不变
        $scores = array_reverse($scores, true);

        // 只获取我们想要的数量
        $scores = array_slice($scores, 0, $userNumber, true);

        // 新建一个空集合y
        $active_users = collect();

        foreach ($scores as $user_id => $user) {
            // 找寻下是否可以找到用户
            $user = $this->userRepository->find($user_id);

            // 如果数据库里有该用户的话
            if ($user) {

                // 将此用户实体放入集合的末尾
                $active_users->push($user);
            }
        }

        // 返回数据
        return $active_users;
    }
}
