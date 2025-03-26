<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with full access to the system'
            ],
            [
                'name' => 'Salesperson',
                'slug' => 'salesperson',
                'description' => 'Sales representative with access to sales-related features'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
