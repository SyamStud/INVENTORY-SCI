<?php

namespace Database\Seeders;

use App\Models\BranchOffice;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        BranchOffice::create([
            'name' => 'Branch Office 1',
            'address' => 'Jl. Raya No. 1',
            'phone' => '08123456789',
            'email' => 'branch1@gmail.com',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'branch_id' => 1,
        ]);
    }
}
