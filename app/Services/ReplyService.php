<?php


namespace App\Services;


use App\Contracts\Repositories\ReplyRepository;
use App\Repositories\Eloquent\ReplyRepositoryEloquent;
use Illuminate\Http\Request;

class ReplyService
{
    /**
     * @var ReplyRepositoryEloquent
     */
    private $repository;

    public function __construct(ReplyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleSearchItem($id)
    {
        return $this->repository->find($id);
    }

    public function handleCreateItem(Request $request)
    {
        $attributes = [
            'content' => $request->get('content'),
            'user_id' => $request->user()->id,
            'topic_id' => $request->get('topic_id'),
        ];

        return $this->repository->create($attributes);
    }

    public function handleDeleteItem($id)
    {
        return $this->repository->delete($id);
    }
}
