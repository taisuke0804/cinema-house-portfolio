<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Common\MovieService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Services\Admin\MovieService as AdminMovieService;

class MovieController extends Controller
{
    protected $movieService;
    protected $adminMovieService;

    public function __construct(MovieService $movieService, AdminMovieService $adminMovieService)
    {
        // MovieServiceクラスのインスタンスを生成
        $this->movieService = $movieService;
        $this->adminMovieService = $adminMovieService;
    }

    public function show(int $id): Response
    {
        $movie = $this->adminMovieService->getMovieById($id);

        $liked = $this->movieService->isLikedByUser($movie);
        
        return Inertia::render('user/movies/Show', [
            'movie' => [
                'id' => $movie->id,
                'title' => $movie->title,
                'genre_label' => $movie->genre->getLabel(),
                'description' => $movie->description,
                'poster_url' => $movie->poster_url,
                'liked' => $liked,
                'like_count' => $movie->likedByUsers()->count(),
            ],
        ]);
    }

    public function toggle(int $id): RedirectResponse
    {
        $likeResult = $this->movieService->toggleLike($id);

        return back()->with($likeResult);
    }
}
