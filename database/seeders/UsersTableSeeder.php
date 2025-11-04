<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'         => 'Nguyen Van A',
            'email'        => 'nguyenvana@example.com',
            'password'     => bcrypt('123456'),
            'phone_number' => '0123456789',
            'status'       => 'pending',
            'avatar'       => '',
            'address'      => 'Da Nang, Vietnam',
            'role_id'      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name'         => 'Tran Thi B',
            'email'        => 'tranthib@example.com',
            'password'     => bcrypt('123456'),
            'phone_number' => '0987654321',
            'status'       => 'pending',
            'avatar'       => '',
            'address'      => 'Gia Lai, Vietnam',
            'role_id'      => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name'         => 'Le Viet Huan',
            'email'        => 'huan@example.com',
            'password'     => bcrypt('123456'),
            'phone_number' => '0987654321',
            'status'       => 'pending',
            'avatar'       => '',
            'address'      => 'Quang Binh, Vietnam',
            'role_id'      => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
