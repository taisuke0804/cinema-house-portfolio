<?php

namespace App\Services\Admin\Api;

use App\Models\Screening;

class ScreeningSeatService
{
    public function getSeatStatus(Screening $screening): array
    {
        $seats = $screening->seats()
            ->orderBy('row')
            ->orderBy('number')
            ->get();

        $totalSeats = $seats->count();
        $reservedSeats = $seats->where('is_reserved', true)->count();
        $availableSeats = $totalSeats - $reservedSeats;
        $occupancyRate = $totalSeats > 0
            ? (int) round(($reservedSeats / $totalSeats) * 100)
            : 0;

        return [
            'screening_id' => $screening->id,
            'total_seats' => $totalSeats,
            'reserved_seats' => $reservedSeats,
            'available_seats' => $availableSeats,
            'occupancy_rate' => $occupancyRate,
            'seats' => $seats->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'row' => $seat->row,
                    'number' => $seat->number,
                    'label' => $seat->row . '-' . $seat->number,
                    'is_reserved' => (bool) $seat->is_reserved,
                ];
            })->values()->all(),
        ];
    }
}
