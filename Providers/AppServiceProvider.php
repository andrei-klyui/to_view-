<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Carbon::setLocale(config('app.locale'));

        $this->registerObservers();

        // Logging sql query to storage/log/laravel.log
        if (config('database.log_to_file')) {
            \DB::listen(function ($query) {
                \Log::info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
            });
        }
    }

    /**
     * Register observers for models.
     */
    private function registerObservers()
    {
        \App\Models\Issue::observe([
            \App\Observers\IssueReadNotification::class,
        ]);

        \App\Models\Post::observe([
            \App\Observers\PostReadNotification::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
        $this->app->register(RepositoryServiceProvider::class);

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
