<?php

namespace App\Http\Controllers;

use App\Services\LinkService;
use App\Services\TopicService;
use App\Services\UserService;
use Illuminate\Http\Request;

class HomeController extends Controller
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

        return view('topics.index', compact('topics', 'active_users', 'links'));
    }
}
