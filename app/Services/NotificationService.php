<?php


namespace App\Services;


use Illuminate\Http\Request;

class NotificationService
{
    public function handleSearchList(Request $request)
    {
        return $request->user()->notifications()->paginate(20);
    }

    public function markAsRead(Request $request)
    {
        $request->user()->markAsRead();
    }
}
