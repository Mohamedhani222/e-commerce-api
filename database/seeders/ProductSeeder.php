<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('products')->delete();
        Product::create([
            'created_by' => User::pluck('id')->random(),
            "name" => "product 2",
            "description" => "hello",
            "image" => "Hsmflsm",
            "price" => 100.22,
            "countInStock" => 55,
            "category_id" => Category::pluck('id')->random(),
        ]);


    }
}
