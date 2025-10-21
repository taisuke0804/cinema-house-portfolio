<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\MovieService;
use Inertia\Inertia;
use App\Http\Requests\MovieSearchRequest;
use App\Http\Requests\Admin\StoreMovieRequest;
use App\Enums\Genre;
use Inertia\Response;

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
            'genres' => Genre::options(),
        ]);
    }

    /**
     * 映画の新規登録画面を表示
     */
    public function create()
    {
        return Inertia::render('admin/movies/Create', [
            'genres' => Genre::options(),
        ]);
    }

    /**
     * 映画の新規登録処理
     */
    public function store(StoreMovieRequest $request)
    {
        $this->movieService->storeMovie($request->validated());

        return redirect()->route('admin.movies.index')->with('success', '映画の新規登録が完了しました');
    }

    /**
     * 映画の詳細を表示
     */
    public function show(int $id): Response
    {
        $movie = $this->movieService->getMovieById($id);
        
        return Inertia::render('admin/movies/Show', [
            'movie' => [
                'id' => $movie->id,
                'title' => $movie->title,
                'genre_label' => $movie->genre->getLabel(),
                'description' => $movie->description,
                'like_count' => $movie->likedByUsers()->count(),
            ],
        ]);
    }
}
