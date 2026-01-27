<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\KendaraanTipe;
use App\Models\Role;
use App\Models\TipeKendaraan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $roles = ['admin', 'petugas', 'owner'];
        foreach ($roles as $item) {
            Role::firstOrCreate([
                'role' => $item
            ]);
        }

        $adminRole = Role::where('role', 'admin')->first();
        User::firstOrCreate([
            'name'     => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role_id'  => $adminRole->id,
        ]);
        $petugasRole = Role::where('role', 'petugas')->first();
        User::firstOrCreate([
            'name'     => 'Petugas',
            'username' => 'petugas',
            'password' => Hash::make('admin123'),
            'role_id'  => $petugasRole->id,
        ]);

        $kendaraanTipe = ['Motor', 'Mobil', 'Bus'];
        foreach ($kendaraanTipe as $item) {
            KendaraanTipe::firstOrCreate([
                'tipe_kendaraan' => $item
            ]);
        }
    }
}
