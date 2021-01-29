<?php


namespace App\Services;


use App\Contracts\Repositories\TopicRepository;
use App\Repositories\Criteria\TopicCriteria;
use App\Repositories\Eloquent\TopicRepositoryEloquent;
use Illuminate\Http\Request;

class TopicService
{
    /**
     * @var TopicRepositoryEloquent
     */
    private $repository;

    public function __construct(TopicRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleSearchList(Request $request)
    {
        $this->repository->pushCriteria(new TopicCriteria($request));

        return $this->repository->with(['user', 'category'])->paginate();
    }
}
