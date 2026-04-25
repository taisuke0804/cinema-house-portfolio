<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;
use App\Models\Movie;
use App\Models\Screening;
use App\Models\Seat;

class SeatReservationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_user_can_reserve_available_seat(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $movie = Movie::factory()->create();

        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
        ]);

        $seat = Seat::factory()->create([
            'screening_id' => $screening->id,
            'is_reserved' => false,
            'user_id' => null,
        ]);

        $response = $this->post(route('user.seats.reserve'), [
            'screening_id' => $screening->id,
            'seat_ids' => [$seat->id],
        ]);

        $response->assertRedirect(route('user.reservation.complete'));

        $this->assertDatabaseHas('seats', [
            'id' => $seat->id,
            'screening_id' => $screening->id,
            'is_reserved' => true,
            'user_id' => $user->id,
        ]);
    }

    /**
     * 異常系テスト
     * 予約済みの座席に対して予約リクエストを送ると失敗する
     */
    public function test_user_cannot_reserve_already_reserved_seat(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $movie = Movie::factory()->create();

        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
        ]);

        $otherUser = User::factory()->create();

        $seat = Seat::factory()->create([
            'screening_id' => $screening->id,
            'is_reserved' => true,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->post(route('user.seats.reserve'), [
            'screening_id' => $screening->id,
            'seat_ids' => [$seat->id],
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('seats', [
            'id' => $seat->id,
            'is_reserved' => true,
            'user_id' => $otherUser->id,
        ]);
    }
}
