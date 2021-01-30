<?php

namespace App\Http\Controllers;

use App\Repositories\Models\Category;
use App\Repositories\Models\Topic;
use App\Services\LinkService;
use App\Services\UserService;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var LinkService
     */
    private $linkService;

    public function __construct(UserService $userService, LinkService $linkService)
    {
        $this->userService = $userService;
        $this->linkService = $linkService;
    }

    public function show(Category $category, Request $request, Topic $topic)
    {
        // 读取分类 ID 关联的话题，并按每 20 条分页
        $topics = $topic->withOrder($request->order)
            ->where('category_id', $category->id)
            ->with('user', 'category')  // 预加载防止 N+1 问题
            ->paginate(20);

        $active_users = $this->userService->handleActiveUsers();
        $links = $this->linkService->handleSearchAll();

        return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }
}
