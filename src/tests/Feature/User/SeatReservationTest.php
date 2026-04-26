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

    /**
     * 最大3席までの座席予約しか出来ないことの検証
     */
    public function test_user_cannot_reserve_more_than_three_seats(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $movie = Movie::factory()->create();

        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
        ]);

        $seatIds = [];
        for ($i=0; $i < 4; $i++) {
            $seat = Seat::factory()->create([
                'screening_id' => $screening->id,
                'row' => 'A',
                'number' => $i + 1,
                'is_reserved' => false,
                'user_id' => null,
            ]);
            $seatIds[] = $seat->id;
        }

        $response = $this->post(route('user.seats.reserve'), [
            'screening_id' => $screening->id,
            'seat_ids' => $seatIds,
        ]);

        $response->assertRedirectBackWithErrors([
            'seat_ids'
        ], 'message');

        foreach ($seatIds as $seatId) {
            $this->assertDatabaseHas('seats', [
                'id' => $seatId,
                'screening_id' => $screening->id,
                'is_reserved' => false,
                'user_id' => null,
            ]);
        }
    }

    /**
     * 複数席の中に1つでも予約済みが混ざっていたら全体の予約が失敗することを検証
     */
    public function test_user_cannot_reserve_seats_reserved_by_other_users(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $movie = Movie::factory()->create();

        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
        ]);

        $availableSeats = Seat::factory()->count(2)->create([
            'screening_id' => $screening->id,
            'is_reserved' => false,
            'user_id' => null,
        ]);

        $otherUser = User::factory()->create();

        $reservedSeat = Seat::factory()->create([
            'screening_id' => $screening->id,
            'is_reserved' => true,
            'user_id' => $otherUser->id,
        ]);

        $seatIds = array_merge(
            $availableSeats->pluck('id')->toArray(),
            [$reservedSeat->id]
        );

        $response = $this->post(route('user.seats.reserve'), [
            'screening_id' => $screening->id,
            'seat_ids' => $seatIds,
        ]);

        $response->assertStatus(422);

        // 空席だった2席は予約されないことを確認
        foreach ($availableSeats as $seat) {
            $this->assertDatabaseHas('seats', [
                'id' => $seat->id,
                'screening_id' => $screening->id,
                'is_reserved' => false,
                'user_id' => null,
            ]);
        }

        // 予約済み席はそのままであることを確認
        $this->assertDatabaseHas('seats', [
            'id' => $reservedSeat->id,
            'screening_id' => $screening->id,
            'is_reserved' => true,
            'user_id' => $otherUser->id,
        ]);
    }

    /**
     * 正常系テスト
     * 3席以内であれば、複数席をまとめて予約できることを検証
     */
    public function test_user_can_reserve_multiple_seats_within_limit(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $movie = Movie::factory()->create();

        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
        ]);

        $seatIds = [];

        for ($i = 0; $i < 3; $i++) {
            $seat = Seat::factory()->create([
                'screening_id' => $screening->id,
                'row' => 'A',
                'number' => $i + 1,
                'is_reserved' => false,
                'user_id' => null,
            ]);

            $seatIds[] = $seat->id;
        }

        $response = $this->post(route('user.seats.reserve'), [
            'screening_id' => $screening->id,
            'seat_ids' => $seatIds,
        ]);

        $response->assertRedirect(route('user.reservation.complete'));

        foreach ($seatIds as $seatId) {
            $this->assertDatabaseHas('seats', [
                'id' => $seatId,
                'screening_id' => $screening->id,
                'is_reserved' => true,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * 別上映回の座席は予約できないことを検証
     */
    public function test_user_cannot_reserve_seat_from_different_screening(): void
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
            'row' => 'A',
            'number' => 1,
            'is_reserved' => false,
            'user_id' => null,
        ]);

        $anotherScreening = Screening::factory()->create([
            'movie_id' => $movie->id,
        ]);

        $response = $this->post(route('user.seats.reserve'), [
            'screening_id' => $anotherScreening->id,
            'seat_ids' => [$seat->id],
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('seats', [
            'id' => $seat->id,
            'screening_id' => $screening->id,
            'is_reserved' => false,
            'user_id' => null,
        ]);

    }
}
