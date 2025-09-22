<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\MovieService;
use Inertia\Inertia;
use App\Http\Requests\MovieSearchRequest;

class MovieController extends Controller
{
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        // MovieServiceクラスのインスタンスを生成
        $this->movieService = $movieService;
    }

    /**
     * 映画の一覧を表示
     */
    public function index(MovieSearchRequest $request)
    {
        $movies = $this->movieService->getMovies($request);
        
        return Inertia::render('admin/movies/Index', [
            'movies' => $movies,
            'filters' => $request->only(['title', 'genre', 'description', 'search_type']),
            'genres' => \App\Enums\Genre::options(),
        ]);
    }

    /**
     * 映画の新規登録画面を表示
     */
    public function create()
    {
        return Inertia::render('admin/movies/Create', [
            'genres' => \App\Enums\Genre::options(),
        ]);
    }
}
