<?php

namespace App\Services\Common;

use App\Models\Movie;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MovieService
{
    /**
     * 現在ログイン中のユーザーを取得
     */
    private function user(): ?User
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('web')->user();
        return $user;
    }

    /**
     * 映画の詳細を取得
     */
    public function getMovieById(int $id): Movie
    {
        $movie = Movie::select('id', 'title', 'description', 'genre')
            ->findOrFail($id);

        return $movie;
    }

    /**
     * 映画のいいね状態を取得
     */
    public function isLikedByUser(Movie $movie): bool
    {
        $user = $this->user();
        return $user ? $user->likedMovies()->where('movie_id', $movie->id)->exists() : false;
    }

    /**
     * いいねトグル処理
     * 
     * @return array{liked: bool, like_count: int}
     */
    public function toggleLike(int $movieId): array
    {
        $movie = Movie::findOrFail($movieId);
        $user = $this->user();

        if (! $user) {
            abort(403, 'ログインユーザーのみ操作可能です');
        }

        // トグル処理
        if ($user->likedMovies()->where('movie_id', $movie->id)->exists()) {
            $user->likedMovies()->detach($movie->id);
            $liked = false;
        } else {
            $user->likedMovies()->attach($movie->id);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'like_count' => $movie->likedByUsers()->count(),
        ];
    }
}