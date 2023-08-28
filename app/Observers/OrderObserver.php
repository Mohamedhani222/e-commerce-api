<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    public function created(Order $order): void
    {
        Cache::forget('orders');

    }

    public function updated(Order $order): void
    {
        Cache::forget('orders');

    }

    public function deleted(Order $order): void
    {
        Cache::forget('orders');

    }

    public function restored(Order $order): void
    {
        Cache::forget('orders');

    }

    public function forceDeleted(Order $order): void
    {
        Cache::forget('orders');

    }
}
