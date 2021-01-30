<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ReplyRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Models\Reply;
use App\Repositories\Validators\ReplyValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ReplyRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class ReplyRepositoryEloquent extends BaseRepository implements ReplyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Reply::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    // 查询过去指定时间有发表过回复的用户
    public function queryPastUsers($passDays)
    {
        return $this->model::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($passDays))
            ->groupBy('user_id')
            ->get();
    }

}
