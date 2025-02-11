<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Broadcasting\SocketIoBroadcaster;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::extend('socketio', function ($app) {
            return new SocketIoBroadcaster();
        });
    }
}
