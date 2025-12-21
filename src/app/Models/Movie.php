<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Genre;

class Movie extends Model
{
    /** @use HasFactory<\Database\Factories\MovieFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'genre',
        'description',
        'poster_path',
    ];

    protected function casts(): array
    {
        return [
            'genre' => Genre::class,
        ];
    }

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }
}
