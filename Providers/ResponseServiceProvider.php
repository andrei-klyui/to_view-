<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Responses\ResponseGeneral;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerStructuredResponse();
    }

    /**
     * Register the structured response for API
     *
     * @return void
     */
    protected function registerStructuredResponse()
    {
        $this->app->singleton(ResponseGeneral::class);
    }
}
