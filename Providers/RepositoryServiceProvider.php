<?php

namespace App\Providers;

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
        $this->app->bind(\App\Repositories\IssueRepository::class, \App\Repositories\IssueRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AttachmentRepository::class, \App\Repositories\AttachmentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CategoryRepository::class, \App\Repositories\CategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ExecutorRepository::class, \App\Repositories\ExecutorRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PostRepository::class, \App\Repositories\PostRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CommentRepository::class, \App\Repositories\CommentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OfficeRepository::class, \App\Repositories\OfficeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StatisticRepository::class, \App\Repositories\StatisticRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\NotificationsRepository::class, \App\Repositories\NotificationsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionRepository::class, \App\Repositories\PermissionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\MenuRepository::class, \App\Repositories\MenuRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RoleRepository::class, \App\Repositories\RoleRepositoryEloquent::class);
        //:end-bindings:
    }
}
