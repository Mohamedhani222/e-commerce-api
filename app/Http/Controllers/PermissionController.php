<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use ApiResponseHelpers;

    public function __construct()
    {

    }

    public function index()
    {
        return Cache::remember('permissions', now()->addMinutes(60), function () {
            return PermissionResource::collection(Permission::all());
        });
    }


    public function store(Request $request)
    {
        try {
            $perm = Permission::create([
                'name' => $request->name
            ]);
            return $this->respondCreated([
                'message' => 'Permission Created Successfully',
                'permission' => PermissionResource::make($perm)
            ]);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public function show(string $id)
    {

    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
