<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Movie;
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
}
