<?php

namespace App\Providers;

use App\Services\ChatGpt\Service as ChatGpt;
use Illuminate\Support\ServiceProvider;

class ChatGptServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->bind('chatGpt', function () {
            return new ChatGpt;
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
