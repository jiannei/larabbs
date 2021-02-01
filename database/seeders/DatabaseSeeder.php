<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // 开发环境填充测试数据
    protected $localSeeders = [
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        UsersTableSeeder::class,// 放在 RolesTableSeeder 后面（依赖关系）
        CategoriesSeeder::class,
        LinksTableSeeder::class,
        TopicsTableSeeder::class,
        RepliesTableSeeder::class,
    ];

    // 生产环境填充数据
    protected $productionSeeders = [
        CategoriesSeeder::class,
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
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
