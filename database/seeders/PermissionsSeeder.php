<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


        function getModelNames(): array
        {
            $path =app_path('Models') . '/*.php';
            return collect(glob($path))->map(fn($file) => basename($file, '.php'))->toArray();
        }

        $processess = ['create', 'update', 'delete','list'];
        // permission for admin
        foreach (getModelNames() as $modelName) {
            foreach ($processess as $process) {
                Permission::create([
                    'name' => "$process " . strtolower($modelName)
                ]);

            }

        }
        $user_roles = [
            'create order',
            'update order'
        ];

        $user_role = Role::where('name', 'user')->first();
        $user_role->syncPermissions($user_roles);

//        $admin_rules =Permission::pluck('id')->get();
//        $admin_rules->givePermissionTo($admin_rules);

        $super_admin = Role::where('name', 'SuperAdmin')->first();
        $super_admin_rules = Permission::pluck('name');
        $super_admin->syncPermissions($super_admin_rules);


    }
}
