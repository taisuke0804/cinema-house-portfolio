<?php

namespace App\Services\Admin;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class MovieService
{
    /**
     * 映画の一覧を取得
     */
    public function getMovies(Request $request): LengthAwarePaginator
    {
        $query = Movie::query();

        // 検索条件に一致する映画情報を取得
        $query = $this->searchMoviesQuery($query, $request);

        $movies = $query->select('id', 'title', 'description', 'genre')
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString(); // クエリ文字列値をペジネーションリンクに追加する(検索用)
        
        $movies->transform(function ($movie) {
            $movie->genre_label = $movie->genre->getLabel(); // ジャンルのラベル取得
            return $movie;
        });

        return $movies;
    }

    /**
     * 映画情報を検索するクエリを生成
     */
    private function searchMoviesQuery(Builder $query, Request $request): Builder
    {
        // タイトル検索（完全一致・あいまい）
        if ($request->filled('title')) {
            if ($request->input('search_type') === 'exact') {
                $query->where('title', $request->input('title'));
            } else {
                $query->where('title', 'LIKE', '%' . $request->input('title') . '%');
            }
        }

        // ジャンル検索
        if ($request->filled('genre')) {
            $query->where('genre', $request->input('genre'));
        }

        // 説明文検索
        if ($request->filled('description')) {
            $query->where('description', 'LIKE', '%' . $request->input('description') . '%');
        }

        return $query;
    }
}