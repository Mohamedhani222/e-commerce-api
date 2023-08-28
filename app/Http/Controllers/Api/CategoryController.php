<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'can:create Category|update Category|delete Category'])->except('index');
    }

    public function index()
    {
        return response()->json([
            'categories' => Category::all(),
        ], 200);
    }

    public function store()
    {

    }
}
