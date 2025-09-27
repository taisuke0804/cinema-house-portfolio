<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Screening;
use App\Models\Seat;

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
    }
}
