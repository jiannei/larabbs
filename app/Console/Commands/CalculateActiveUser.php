<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class CalculateActiveUser extends Command
{
    // 供我们调用命令
    protected $signature = 'larabbs:calculate-active-user';

    // 命令的描述
    protected $description = '生成活跃用户';

    // 最终执行的方法
    public function handle(UserService $service)
    {
        // 在命令行打印一行信息
        $this->info("开始计算...");

        $service->handleActiveUsers(true);

        $this->info("成功生成！");
    }
}
