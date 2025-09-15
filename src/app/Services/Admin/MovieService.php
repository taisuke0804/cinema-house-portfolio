<?php

namespace App\Services\Admin;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MovieService
{
    /**
     * 映画の一覧を取得
     */
    public function getMovies(Request $request): LengthAwarePaginator
    {
        $query = Movie::query();

        $movies = $query->select('id', 'title', 'description', 'genre')
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString(); // クエリ文字列値をペジネーションリンクに追加する(検索用)
        
        return $movies;
    }
}