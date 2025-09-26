<?php

namespace App\Services\Admin;
use App\Models\Screening;

class ScreeningService
{
    /**
     * 上映スケジュールの新規登録
     */
    public function storeScreening(array $validated): void
    {
        // 日付 + 時刻を datetime に組み立て
        $validated['start_time'] = $validated['screening_date'] . ' ' . $validated['start_time'] . ':00';
        $validated['end_time']   = $validated['screening_date'] . ' ' . $validated['end_time'] . ':00';

        // DBに不要なフィールドを削除
        unset($validated['screening_date']);

        Screening::create($validated);
    }
}