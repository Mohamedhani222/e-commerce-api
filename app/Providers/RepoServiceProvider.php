<?php

namespace App\Providers;

use App\Http\interfaces\OrderInterface;
use App\Http\Repositories\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            OrderInterface::class,
            OrderRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
