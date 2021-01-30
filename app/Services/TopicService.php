<?php


namespace App\Services;


use App\Contracts\Repositories\TopicRepository;
use App\Repositories\Criteria\TopicCriteria;
use App\Repositories\Eloquent\TopicRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function handleSearchItem($id)
    {
        return $this->repository->find($id);
    }

    public function handleUpdateItem(Request $request, $id)
    {
        $attributes = $request->all();

        return $this->repository->update($attributes, $id);
    }

    public function handleDeleteItem($id)
    {
        return $this->repository->delete($id);
    }

    public function handleCreateItem(Request $request)
    {
        $attributes = array_merge($request->all(), ['user_id' => Auth::id()]);

        return $this->repository->create($attributes);
    }
}
