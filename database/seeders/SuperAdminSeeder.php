<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Role::firstOrCreate(['name' => 'super_admin']);

        $user = User::firstOrCreate(
            ['email' => 'sadmin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123456789')
            ]
        );

        $user->assignRole('super_admin');


        // Second super admin
        $user2 = User::firstOrCreate(
            ['email' => 'pai@esamudaay.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('IntraNest2025$')
            ]
        );
        $user2->assignRole('super_admin');
    }
}
