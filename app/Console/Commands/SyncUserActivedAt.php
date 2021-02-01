<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class SyncUserActivedAt extends Command
{
    protected $signature = 'larabbs:sync-user-actived-at';
    protected $description = '将用户最后登录时间从 Redis 同步到数据库中';

    public function handle(UserService $service)
    {
        $service->handleSyncActiveTime();
        $this->info("同步成功！");
    }
}
