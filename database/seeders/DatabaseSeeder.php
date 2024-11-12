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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        BranchOffice::create([
            'name' => 'Branch Office 1',
            'code' => 'BO001',
            'province_id' => '11',
            'regency_id' => '1103',
            'district_id' => '1101010',
            'village_id' => '1101010002',
        ]);

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'branch_id' => 1,
        ]);

        Position::create([
            'name' => 'Kacab',
            'branch_id' => 1,
        ]);

        Employee::create([
            'name' => 'Kacab Branch Office 1',
            'npp' => 'NPP001',
            'position_id' => 1,
            'branch_id' => 1,
        ]);

        Unit::create([
            'name' => 'Unit 1',
            'branch_id' => 1,
        ]);

        Item::create([
            'name' => 'Item 1',
            'price' => 10000,
            'unit_id' => 1,
            'branch_id' => 1,
        ]);
    }
}
