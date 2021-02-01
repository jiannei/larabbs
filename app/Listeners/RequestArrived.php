<?php

namespace App\Listeners;

use App\Jobs\RecordActiveTime;
use Jiannei\Logger\Laravel\Events\RequestArrivedEvent;

class RequestArrived
{
    /**
     * Handle the event.
     *
     * @param  RequestArrivedEvent  $event
     * @return void
     */
    public function handle(RequestArrivedEvent $event)
    {
        // 记录用户最后活跃时间
        dispatch(new RecordActiveTime($event->request->user()));
    }
}
