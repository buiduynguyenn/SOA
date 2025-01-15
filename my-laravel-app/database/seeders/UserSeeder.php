<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'UserName' => 'admin',
            'Password' => Hash::make('123456')  // Mật khẩu: 123456
        ]);

        User::create([
            'UserName' => 'user1',
            'Password' => Hash::make('123456')
        ]);

        // Thêm nhiều user khác nếu cần
    }
} 