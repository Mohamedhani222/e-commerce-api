<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponseHelpers;

    public function __construct()
    {
    }

    public function index()
    {
        return Cache::remember('roles', now()->addMinutes(60), function () {
            return Role::all();
        });

    }

    public function store(RoleRequest $request)
    {
        try {
            $permissions = $request->permissions;
            if (Permission::whereIn('id', $permissions)->get()) {
                $role = Role::create(['name' => $request->name]);
                $role->syncPermissions($permissions);
                return response()->json([
                    'message' => 'Role has been created successfully',
                    'role' => $role
                ]);
            }
            return response()->json([
                'message' => 'Permission not exist ',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);

        }
    }

    public function show(Role $role)
    {
        try {
            return $role;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

    }


    public function update(RoleRequest $request,$id)
    {
        try {
            $role =Role::findOrFail($id);
            $role ->name =$request->name;
            $role->save();

            $role->syncPermissions($request->permissions);
            return $this->respondWithSuccess([
                'message' => 'role updated successfully',
                'role' => $role

            ]);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

    }

    public function destroy(string $id)
    {
        try {
           $role = Role::findOrFail($id);
           $role->permissions()->delete();
           $role->delete();
            return $this->respondWithSuccess(['message'=>'role deleted successfully']);
        }catch (\Throwable $e) {
            return $e->getMessage();
        }


    }
}
