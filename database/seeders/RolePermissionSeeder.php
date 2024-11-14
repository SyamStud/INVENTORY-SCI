<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Unit;
use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BranchOffice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'head-of-branch']);
        Role::create(['name' => 'employee']);

        Permission::create(['name' => 'manage-users']);
        Permission::create(['name' => 'manage-items']);
        Permission::create(['name' => 'manage-employees']);
        Permission::create(['name' => 'manage-assets']);
    }
}
