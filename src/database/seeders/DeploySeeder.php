<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class DeploySeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['production', 'staging'])) return;

        Admin::factory()->create([
            'name' => '管理者太郎',
            'email' => env('ADMIN_EMAIL', 'admin@gmail.com'),
            'email_verified_at' => now(),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin1234')),
            'remember_token' => Str::random(10),
        ]);

        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => env('TEST_USER_EMAIL', 'test@gmail.com'),
            'password' => Hash::make(env('TEST_USER_PASSWORD', '1111aaaa')),
        ]);

        User::factory()->count(50)->create();

        $this->call([
            MovieSeeder::class,
            ScreeningSeeder::class,
            SeatSeeder::class,
        ]);

    }
}
