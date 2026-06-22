<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'description',
        'ingredients', 'steps', 'image', 'cook_time_minutes'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function ratings() {
        return $this->hasMany(Rating::class);
    }

    public function averageRating() {
        return $this->ratings()->avg('stars');
    }
}
