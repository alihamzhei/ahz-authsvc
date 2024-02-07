<?php

namespace Src\Common\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Common\Application\Services\ResponseService;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('response-svc', function(){
            return new ResponseService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
