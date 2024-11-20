<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature = 'create:super-admin';
    protected $description = 'Membuat user dengan role super admin';

    public function handle()
    {
        $email = $this->ask('Masukkan email untuk Super Admin');
        $password = $this->secret('Masukkan password untuk Super Admin');

        try {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'super_admin',
            ]);

            $user->assignRole('super-admin');

            $this->info('Super Admin berhasil dibuat!');
            $this->info('Email: ' . $email);
        } catch (\Exception $e) {
            $this->error('Gagal membuat Super Admin: ' . $e->getMessage());
        }
    }
}
