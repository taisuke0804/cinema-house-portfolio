<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Database\Seeders\MovieSeeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        Admin::factory()->create([
            'name' => '管理者太郎',
            'email' => env('ADMIN_EMAIL', 'admin@gmail.com'),
            'email_verified_at' => now(),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin1234')),
            'remember_token' => Str::random(10),
        ]);

        User::factory()->create([
            'name' => 'テスト',
            'email' => 'test@gmail.com',
            'password' => Hash::make('1111aaaa'),
            'remember_token' => Str::random(10),
        ]);

        $this->call([
            MovieSeeder::class,
        ]);
    }
}
