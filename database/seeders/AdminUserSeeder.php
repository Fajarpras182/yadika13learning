<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if any admin user already exists
        $adminExists = User::where('role', 'admin')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'), // Change password to a secure one after first login
                'role' => 'admin',
                'is_active' => true,
                'nis_nip' => '000000',
                'kelas' => null,
                'jurusan' => null,
                'no_hp' => null,
                'alamat' => null,
            ]);
        }
    }
}
