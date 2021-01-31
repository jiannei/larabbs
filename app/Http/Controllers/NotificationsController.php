<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * @var NotificationService
     */
    private $service;

    public function __construct(NotificationService $service)
    {
        $this->middleware('auth');

        $this->service = $service;
    }

    public function index(Request $request)
    {
        $notifications = $this->service->handleSearchList($request);

        $this->service->markAsRead($request);

        return view('notifications.index', compact('notifications'));
    }
}
