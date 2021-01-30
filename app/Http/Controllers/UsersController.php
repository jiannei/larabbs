<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;

class UsersController extends Controller
{
    /**
     * @var UserService
     */
    private $service;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth', ['except' => ['show']]);

        $this->service = $userService;
    }

    public function show($id)
    {
        $user = $this->service->handleSearchItem($id);

        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = $this->service->handleSearchItem($id);

        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, $id)
    {
        $user = $this->service->handleSearchItem($id);

        $this->authorize('update', $user);

        $this->service->handleUpdateItem($request, $id);

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
