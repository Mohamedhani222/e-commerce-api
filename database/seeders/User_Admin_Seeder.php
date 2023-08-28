<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class User_Admin_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();
        DB::table('roles')->delete();
        DB::table('role_has_permissions')->delete();
        $super_admin = User::create([
            'name' => 'mohamed',
            'email' => 'super_admin@gmail.com',
            'password' => Hash::make('12345678')
        ]);
        $admin = User::create([
            'name' => 'mohamed',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678')
        ]);
        $user = User::create([
            'name' => 'mohamed',
            'email' => 'user@gmail.com',
            'password' => Hash::make('12345678')
        ]);

        $superAdmin_role = Role::create([
            'name' => 'SuperAdmin'
        ]);

        $admin_role = Role::create([
            'name' => 'admin'
        ]);
        $user_role = Role::create([
            'name' => 'user'
        ]);

        $super_admin->assignRole($superAdmin_role);
        $user->assignRole($user_role);
        $admin->assignRole($admin_role);
    }
}
