<?php

namespace App\Http\Controllers;

use App\Services\LinkService;
use App\Services\TopicService;
use App\Services\UserService;
use Illuminate\Http\Request;

class PagesController extends Controller
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

    public function __construct(TopicService $topicService, UserService $userService, LinkService $linkService)
    {
        $this->middleware('auth', ['except' => ['home']]);

        $this->topicService = $topicService;
        $this->userService = $userService;
        $this->linkService = $linkService;
    }

    public function home(Request $request)
    {
        $topics = $this->topicService->handleSearchList($request);
        $links = $this->linkService->handleSearchAll();

        $active_users = $this->userService->handleActiveUsers();

        return view('pages.home', compact('topics', 'active_users', 'links'));
    }

    public function permissionDenied()
    {
        // 如果当前用户有权限访问后台，直接跳转访问
        if (config('administrator.permission')()) {
            return redirect(url(config('administrator.uri')), 302);
        }

        // 否则使用视图
        return view('pages.permission_denied');
    }
}
