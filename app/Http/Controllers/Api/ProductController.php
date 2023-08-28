<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function App\search_model;
use F9Web\ApiResponseHelpers;

class ProductController extends Controller
{
    use ApiResponseHelpers;

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $products = Cache::remember('products', now()->addMinutes(60), function () {
            return Product::all();
        });
        $search = $request->query('s');

        if ($search) {
            $products = search_model(Product::orderBy('created_at', 'desc'), ['name', 'description'], $search, ['category']);
        }

        return response(ProductResource::collection($products));
    }

    public function store(ProductRequest $request)
    {
        try {
            $pro = array_merge($request->validated(), ['created_by' => auth('sanctum')->user()->id]);

            Product::create($pro);

            return response()->json([
                'message' => 'product created successfully !',
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function show(Product $product)
    {
        try {
            return ProductResource::make($product);
        } catch (\Throwable $e) {
           return $e->getMessage();
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $pro = Product::findorFail($id);

            $pro->update(
                $request->validated());

            $pro->save();

            return response()->json([
                'message' => 'product updated successfully !',
                'product' => $pro
            ], 200);

        } catch (ModelNotFoundException $e) {
            return $e->getMessage();

        }
    }


    public function destroy(string $id)
    {
        if ($pro = Product::findorFail($id)) {
            $pro->delete();
            return response()->json([
                'message' => 'product deleted successfully!',
            ], 200);
        }

        return response()->json([
            'message' => 'product does\'nt exist ',
        ], 404);

    }
}
