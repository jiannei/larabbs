<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // 开发环境填充测试数据
    protected $localSeeders = [
        UsersTableSeeder::class,
        CategoriesSeeder::class,
        LinksTableSeeder::class,
        TopicsTableSeeder::class,
        RepliesTableSeeder::class,
    ];

    // 生产环境填充数据
    protected $productionSeeders = [
        CategoriesSeeder::class
    ];

    public function run(): void
    {
        if (app()->isLocal()) {
            $this->call($this->localSeeders);
        }

        if (app()->isProduction()) {
            $this->call($this->productionSeeders);
        }
    }
}
