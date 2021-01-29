<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\TopicRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Models\Topic;
use App\Repositories\Validators\TopicValidator;

/**
 * Class TopicRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class TopicRepositoryEloquent extends BaseRepository implements TopicRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Topic::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
        // $this->pushCriteria(app(TopicCriteria::class));
    }
}
