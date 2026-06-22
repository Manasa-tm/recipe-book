<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $request->validate(['stars' => 'required|integer|min:1|max:5']);

        Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'recipe_id' => $recipe->id],
            ['stars' => $request->stars]
        );

        return back()->with('success', 'Rating submitted!');
    }
}