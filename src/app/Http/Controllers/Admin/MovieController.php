<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\MovieService;

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
    public function index(Request $request)
    {
        $movies = $this->movieService->getMovies($request);
        dd($movies);
    }
}
