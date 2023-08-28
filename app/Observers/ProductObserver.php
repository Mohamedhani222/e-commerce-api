<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function created(Product $product): void
    {
        Cache::tags(['products'])->flush();

    }


    public function updated(Product $product): void
    {
        Cache::tags(['products'])->flush();

    }

    public function deleted(Product $product): void
    {
        Cache::tags(['products'])->flush();

    }

    public function restored(Product $product): void
    {
        Cache::tags(['products'])->flush();
    }

    public function forceDeleted(Product $product): void
    {
        Cache::tags(['products'])->flush();

    }
}
