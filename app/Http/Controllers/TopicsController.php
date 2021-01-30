<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Services\CategoryService;
use App\Services\LinkService;
use App\Services\TopicService;
use App\Services\UserService;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    /**
     * @var TopicService
     */
    private $topicService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var LinkService
     */
    private $linkService;

    /**
     * @var CategoryService
     */
    private $categoryService;

    public function __construct(TopicService $topicService, UserService $userService, LinkService $linkService, CategoryService $categoryService)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);

        $this->topicService = $topicService;
        $this->userService = $userService;
        $this->linkService = $linkService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $topics = $this->topicService->handleSearchList($request);
        $links = $this->linkService->handleSearchAll();

        $active_users = $this->userService->handleActiveUsers();

        return view('topics.index', compact('topics', 'active_users', 'links'));
    }

    public function show($id, $slug)
    {
        $topic = $this->topicService->handleSearchItem($id);

        // URL 矫正
        if (!empty($topic->slug) && $topic->slug != $slug) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

    public function create()
    {
        $categories = $this->categoryService->handleSearchAll();

        return view('topics.create', compact('categories'));
    }

    public function store(TopicRequest $request)
    {
        $topic = $this->topicService->handleCreateItem($request);

        return redirect()->to($topic->link())->with('success', '帖子创建成功！');
    }

    public function edit($id)
    {
        $topic = $this->topicService->handleSearchItem($id);

        $this->authorize('update', $topic);

        $categories = $this->categoryService->handleSearchAll();

        return view('topics.edit', compact('topic', 'categories'));
    }

    public function update(TopicRequest $request, $id)
    {
        $topic = $this->topicService->handleSearchItem($id);

        $this->authorize('update', $topic);// TODO

        $topic = $this->topicService->handleUpdateItem($request, $id);

        return redirect()->to($topic->link())->with('success', '更新成功！');
    }

    public function destroy($id)
    {
        $topic = $this->topicService->handleSearchItem($id);

        $this->authorize('destroy', $topic);

        $this->topicService->handleDeleteItem($id);

        return redirect()->route('topics.index')->with('success', '成功删除！');
    }
}
