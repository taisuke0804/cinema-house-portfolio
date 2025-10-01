<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Screening;
use App\Models\Seat;
use App\Models\User;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $screenings = Screening::all();
        $rows = ['A', 'B']; // 行
        $seatsPerRow = 10;       // 1行あたりの席数

        foreach ($screenings as $screening) {
            $seats = [];

            foreach ($rows as $row) {
                for ($number = 1; $number <= $seatsPerRow; $number++) {
                    $seats[] = [
                        'screening_id' => $screening->id,
                        'row' => $row,
                        'number' => $number,
                        'is_reserved' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            Seat::insert($seats); // 一括挿入で座席を生成
        }

        // Userモデルからランダムにユーザーを5人取得
        $randomUsers = User::inRandomOrder()->limit(5)->get();

        // 予約済みの座席を生成
        $seats = Seat::inRandomOrder()->limit(5)->get();
        foreach ($seats as $seat) {
            $user = $randomUsers->shift(); // 配列から先頭の要素を取り出す
            $seat->update([
                'user_id' => $user->id,
                'is_reserved' => true,
            ]);
        }
    }
}
