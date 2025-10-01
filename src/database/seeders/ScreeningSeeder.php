<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Screening;
use Illuminate\Support\Facades\DB;

class ScreeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = \Carbon\Carbon::now();

        $tommorow = $now->copy()->addDays(1);
        Screening::factory()->create([
            'movie_id' => DB::table('movies')->find(1)->id,
            'start_time' => $tommorow->copy()->setTime(9, 0, 0),
            'end_time' => $tommorow->copy()->setTime(11, 0, 0),
        ]);

        $today = $now->copy();
        Screening::factory()->create([
            'movie_id' => DB::table('movies')->find(2)->id,
            'start_time' => $today->copy()->setTime(12, 0, 0),
            'end_time' => $today->copy()->setTime(14, 0, 0),
        ]);

        $yesterday = $now->copy()->subDays(1);
        Screening::factory()->create([
            'movie_id' => DB::table('movies')->find(3)->id,
            'start_time' => $yesterday->copy()->setTime(15, 0, 0),
            'end_time' => $yesterday->copy()->setTime(17, 0, 0),
        ]);

        $daysLater10 = $now->copy()->addDays(10);
        Screening::factory()->create([
            'movie_id' => DB::table('movies')->find(4)->id,
            'start_time' => $daysLater10->copy()->setTime(9, 0, 0),
            'end_time' => $daysLater10->copy()->setTime(11, 0, 0),
        ]);

        $daysLater20 = $now->copy()->addDays(20);
        Screening::factory()->create([
            'movie_id' => DB::table('movies')->find(5)->id,
            'start_time' => $daysLater20->copy()->setTime(9, 0, 0),
            'end_time' => $daysLater20->copy()->setTime(11, 0, 0),
        ]);

        $daysAgo = $now->copy()->subDays(15);
        Screening::factory()->create([
            'movie_id' => DB::table('movies')->find(6)->id,
            'start_time' => $daysAgo->copy()->setTime(9, 0, 0),
            'end_time' => $daysAgo->copy()->setTime(11, 0, 0),
        ]);

        // 重複する上映スケジュールが生成される可能性があるため、for文を利用して20件生成する
        for ($i = 0; $i < 20; $i++) {
            Screening::factory()->create();
        }
    }
}
