<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'superadmin',
                'created_at' => now(),
            ],
            [
                'name' => 'admin',
                'created_at' => now(),
            ],
            [
                'name' => 'teacher',
                'created_at' => now(),
            ],
            [
                'name' => 'student',
                'created_at' => now(),
            ],
            [
                'name' => 'parent',
                'created_at' => now(),
            ],
        ]);
    }
}
