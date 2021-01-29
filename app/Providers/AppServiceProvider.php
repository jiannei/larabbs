<?php

namespace App\Providers;

use App\Observers\LinkObserver;
use App\Observers\ReplyObserver;
use App\Observers\TopicObserver;
use App\Observers\UserObserver;
use App\Repositories\Models\Link;
use App\Repositories\Models\Reply;
use App\Repositories\Models\Topic;
use App\Repositories\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RepositoryServiceProvider::class);

        if (app()->isLocal()) {
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Reply::observe(ReplyObserver::class);
        Topic::observe(TopicObserver::class);
        Link::observe(LinkObserver::class);

        Paginator::useBootstrap();

        JsonResource::withoutWrapping();
    }
}
