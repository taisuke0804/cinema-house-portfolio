<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MovieStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ポスター画像ありで映画を登録できること
     */
    public function test_can_store_movie_with_poster(): void
    {
        Storage::fake('public');

        // 管理者を作成してログイン
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // リクエストボディを準備
        $posterImage = UploadedFile::fake()->image('poster.jpg', 200, 300);

        $response = $this->post(route('admin.movies.store'), [
            'title' => 'テスト映画',
            'genre' => 1,  // Action
            'description' => 'これはテスト映画の説明です。',
            'poster' => $posterImage,
        ]);

        // リダイレクト確認
        $response->assertRedirect(route('admin.movies.index'));

        // フラッシュメッセージ確認
        $response->assertSessionHas('success', '映画の新規登録が完了しました');

        // データベースに保存されたことを確認
        $this->assertDatabaseHas('movies', [
            'title' => 'テスト映画',
            'genre' => 1,
            'description' => 'これはテスト映画の説明です。',
        ]);

        // ストレージに画像が保存されたことを確認
        $movie = Movie::where('title', 'テスト映画')->first();
        $this->assertNotNull($movie->poster_path);
        Storage::disk('public')->assertExists($movie->poster_path);
    }

    /**
     * ポスター画像なしで映画を登録できること
     */
    public function test_can_store_movie_without_poster(): void
    {
        // 管理者を作成してログイン
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.movies.store'), [
            'title' => 'ポスターなし映画',
            'genre' => 2,  // Comedy
            'description' => 'ポスター画像なしの映画です。',
            // poster は指定しない
        ]);

        // リダイレクト確認
        $response->assertRedirect(route('admin.movies.index'));

        // フラッシュメッセージ確認
        $response->assertSessionHas('success', '映画の新規登録が完了しました');

        // データベースに保存されたことを確認
        $this->assertDatabaseHas('movies', [
            'title' => 'ポスターなし映画',
            'genre' => 2,
            'description' => 'ポスター画像なしの映画です。',
            'poster_path' => null,  // poster_path が null であることを確認
        ]);
    }

    /**
     * 未認証ユーザーは映画を登録できないこと
     */
    public function test_unauthenticated_user_cannot_store_movie(): void
    {
        $response = $this->post(route('admin.movies.store'), [
            'title' => 'テスト映画',
            'genre' => 1,
            'description' => 'テスト説明',
        ]);

        // ログインページにリダイレクトされることを確認
        $response->assertRedirect(route('admin.login'));

        // データベースに保存されていないことを確認
        $this->assertDatabaseMissing('movies', [
            'title' => 'テスト映画',
        ]);
    }

    /**
     * 一般ユーザーは管理者映画登録できないこと
     */
    public function test_general_user_cannot_store_movie(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $response = $this->post(route('admin.movies.store'), [
            'title' => 'ユーザー禁止映画',
            'genre' => 3,
            'description' => '一般ユーザーからのアクセスは拒否されるべきです。',
        ]);

        $response->assertRedirect(route('admin.login'));

        $this->assertDatabaseMissing('movies', [
            'title' => 'ユーザー禁止映画',
        ]);
    }

    /**
     * 必須項目が未入力の場合、登録できないこと
     */
    public function test_validation_fails_when_required_fields_are_missing(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.movies.store'), [
            // 必須項目を全て空で送信
            'title' => '',
            'genre' => '',
            'description' => '',
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertRedirect();

        // バリデーションエラーがセッションに保持されていることを確認
        $response->assertSessionHasErrors(['title', 'genre', 'description']);

        // データベースに保存されていないことを確認
        $this->assertDatabaseMissing('movies', [
            'title' => '',
        ]);
    }

    /**
     * 不正なジャンルでは映画を登録できないこと
     */
    public function test_validation_fails_with_invalid_genre(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.movies.store'), [
            'title' => '無効ジャンル映画',
            'genre' => 999,
            'description' => 'ジャンルが不正な場合のテストです。',
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertRedirect();

        // genre フィールドのエラーを確認
        $response->assertSessionHasErrors(['genre']);

        // データベースに保存されていないことを確認
        $this->assertDatabaseMissing('movies', [
            'title' => '無効ジャンル映画',
        ]);
    }

    /**
     * 不正なポスター画像では映画を登録できないこと
     */
    public function test_validation_fails_with_invalid_poster_file(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $invalidPoster = UploadedFile::fake()->create('poster.txt', 100, 'text/plain');

        $response = $this->post(route('admin.movies.store'), [
            'title' => '不正ポスター映画',
            'genre' => 1,
            'description' => 'ポスターが画像形式でない場合のテストです。',
            'poster' => $invalidPoster,
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertRedirect();

        // poster フィールドのエラーを確認
        $response->assertSessionHasErrors(['poster']);

        // データベースに保存されていないことを確認
        $this->assertDatabaseMissing('movies', [
            'title' => '不正ポスター映画',
        ]);
    }

    /**
     * ポスター画像サイズが上限を超える場合、登録できないこと
     */
    public function test_validation_fails_when_poster_size_exceeds_limit(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $oversizedPoster = UploadedFile::fake()->create('poster.jpg', 3000, 'image/jpeg');

        $response = $this->post(route('admin.movies.store'), [
            'title' => '大きすぎるポスター映画',
            'genre' => 1,
            'description' => 'ポスター画像が上限を超える場合のテストです。',
            'poster' => $oversizedPoster,
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertRedirect();

        // poster フィールドのエラーを確認
        $response->assertSessionHasErrors(['poster']);

        // データベースに保存されていないことを確認
        $this->assertDatabaseMissing('movies', [
            'title' => '大きすぎるポスター映画',
        ]);
    }
}
