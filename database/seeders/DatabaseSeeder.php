<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Item;
use App\Models\Unit;
use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BranchOffice;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Assert;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        // BranchOffice::create([
        //     'name' => 'Branch Office 1',
        //     'code' => 'BO001',
        //     'province_id' => '11',
        //     'regency_id' => '1103',
        //     'district_id' => '1101010',
        //     'village_id' => '1101010002',
        // ]);

        // Position::create([
        //     'name' => 'Kacab',
        //     'branch_id' => 1,
        // ]);

        // Employee::create([
        //     'name' => 'Kacab Branch Office 1',
        //     'npp' => 'NPP001',
        //     'position_id' => 1,
        //     'branch_id' => 1,
        // ]);

        // $user = User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => bcrypt('12345678'),
        //     'branch_id' => 1,
        //     'employee_id' => 1,
        // ]);

        // $user->assignRole('admin');

        // Unit::create([
        //     'name' => 'Unit 1',
        //     'branch_id' => 1,
        // ]);

        // Item::create([
        //     'name' => 'Item 1',
        //     'price' => 10000,
        //     'unit_id' => 1,
        //     'branch_id' => 1,
        // ]);

        // Brand::create([
        //     'name' => 'Brand 1',
        //     'branch_id' => 1,
        // ]);

        // Asset::create([
        //     'inventory_number' => 'INV001',
        //     'tag_number' => 'TAG001',
        //     'name' => 'Asset 1',
        //     'brand_id' => 1,
        //     'serial_number' => 'SN001',
        //     'color' => 'Red',
        //     'size' => 'Medium',
        //     'condition' => 'baik',
        //     'status' => 'terpakai',
        //     'permit' => 'Permit 1',
        //     'calibration' => json_encode(['calibration_data' => 'data']),
        //     'calibration_number' => 'CAL001',
        //     'calibration_interval' => 12,
        //     'calibration_start_date' => '2023-01-01',
        //     'calibration_due_date' => '2024-01-01',
        //     'calibration_institution' => 'Institution 1',
        //     'calibration_type' => 'Type 1',
        //     'range' => '0-100',
        //     'correction_factor' => 'Factor 1',
        //     'significance' => 'ya',
        //     'photo' => 'photo.jpg',
        //     'branch_id' => 1,
        // ]);
    }
}
