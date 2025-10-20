<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Common\MovieService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class MovieController extends Controller
{
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        // MovieServiceクラスのインスタンスを生成
        $this->movieService = $movieService;
    }

    public function show(int $id): Response
    {
        $movie = $this->movieService->getMovieById($id);

        $liked = $this->movieService->isLikedByUser($movie);
        
        return Inertia::render('user/movies/Show', [
            'movie' => [
                'id' => $movie->id,
                'title' => $movie->title,
                'genre_label' => $movie->genre->getLabel(),
                'description' => $movie->description,
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
