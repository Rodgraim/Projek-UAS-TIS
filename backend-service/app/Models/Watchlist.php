<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $fillable = ['user_id', 'anime_id', 'anime_title', 'anime_rating', 'anime_image', 'status_watching'];

    public function user() { return $this->belongsTo(User::class); }
}