<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplyRequest;
use App\Services\ReplyService;

class RepliesController extends Controller
{
    /**
     * @var ReplyService
     */
    private $service;

    public function __construct(ReplyService $service)
    {
        $this->middleware('auth');

        $this->service = $service;
    }

    public function store(ReplyRequest $request)
    {
        $reply = $this->service->handleCreateItem($request);

        return redirect()->to($reply->topic->link())->with('success', '评论创建成功！');
    }

    public function destroy($id)
    {
        $reply = $this->service->handleSearchItem($id);

        $this->authorize('destroy', $reply);

        $this->service->handleDeleteItem($id);

        return redirect()->to($reply->topic->link())->with('success', '评论删除成功！');
    }
}
