<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoryRepository;
use App\Contracts\Repositories\LinkRepository;
use App\Contracts\Repositories\ReplyRepository;
use App\Contracts\Repositories\TopicRepository;
use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Repositories\Eloquent\LinkRepositoryEloquent;
use App\Repositories\Eloquent\ReplyRepositoryEloquent;
use App\Repositories\Eloquent\TopicRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(TopicRepository::class, TopicRepositoryEloquent::class);
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
        $this->app->bind(LinkRepository::class, LinkRepositoryEloquent::class);
        $this->app->bind(ReplyRepository::class, ReplyRepositoryEloquent::class);
        $this->app->bind(CategoryRepository::class, CategoryRepositoryEloquent::class);
        //:end-bindings:
    }
}
