<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IndianRecipeController extends Controller
{
    private string $base = 'https://www.themealdb.com/api/json/v1/1';

    /**
     * AJAX endpoint: returns Indian recipes, optionally filtered by a search term.
     */
    public function search(Request $request)
    {
        $search = trim((string) $request->search);

        if ($search !== '') {
            // MealDB's search.php doesn't support combining name + area in one call,
            // so we search by name then filter the results down to Indian cuisine only.
            $response = Http::get("{$this->base}/search.php", ['s' => $search]);
            $meals = collect($response->json('meals') ?? [])
                ->filter(fn ($meal) => ($meal['strArea'] ?? null) === 'Indian')
                ->values();
        } else {
            $response = Http::get("{$this->base}/filter.php", ['a' => 'Indian']);
            $meals = collect($response->json('meals') ?? []);
        }

        $meals = $meals->map(function ($meal) {
            return [
                'id' => $meal['idMeal'],
                'title' => $meal['strMeal'],
                'category' => $meal['strCategory'] ?? null,
                'image' => $meal['strMealThumb'] ?? null,
            ];
        })->values();

        return response()->json(['meals' => $meals]);
    }
}