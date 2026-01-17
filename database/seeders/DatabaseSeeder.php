<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'System administrator with full access']
        );

        $courierRole = Role::firstOrCreate(
            ['slug' => 'courier'],
            ['name' => 'Courier', 'description' => 'Delivery personnel who transport specimens']
        );

        $clientRole = Role::firstOrCreate(
            ['slug' => 'client'],
            ['name' => 'Client', 'description' => 'Healthcare facility staff who request specimen transport']
        );

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@neoprolab.com'],
            [
                'role_id' => $adminRole->id,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password123'),
                'phone' => '1234567890',
                'is_active' => true,
            ]
        );

        // Courier user
        User::firstOrCreate(
            ['email' => 'courier@neoprolab.com'],
            [
                'role_id' => $courierRole->id,
                'first_name' => 'John',
                'last_name' => 'Courier',
                'password' => Hash::make('password123'),
                'phone' => '0987654321',
                'is_active' => true,
            ]
        );

        // Client user
        User::firstOrCreate(
            ['email' => 'client@neoprolab.com'],
            [
                'role_id' => $clientRole->id,
                'first_name' => 'Jane',
                'last_name' => 'Client',
                'password' => Hash::make('password123'),
                'phone' => '5551234567',
                'is_active' => true,
            ]
        );
    }
}
