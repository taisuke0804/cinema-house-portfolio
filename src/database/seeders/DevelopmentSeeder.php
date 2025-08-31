<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
    }
}
