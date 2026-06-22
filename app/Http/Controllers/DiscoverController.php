<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DiscoverController extends Controller
{
    // TheMealDB free test API key is "1"
    private string $base = 'https://www.themealdb.com/api/json/v1/1';

    public function index(Request $request)
    {
        $search = $request->search;
        $category = $request->category;

        $meals = collect();

        if ($category) {
            $response = Http::get("{$this->base}/filter.php", ['c' => $category]);
            $meals = collect($response->json('meals') ?? []);
        } elseif ($search) {
            $response = Http::get("{$this->base}/search.php", ['s' => $search]);
            $meals = collect($response->json('meals') ?? []);
        } else {
            // Default: show a random-ish set by searching with empty term (MealDB returns
            // a broad list of meals starting with common letters as a simple "browse all")
            $response = Http::get("{$this->base}/search.php", ['s' => '']);
            $meals = collect($response->json('meals') ?? []);
        }

        $meals = $meals->map(function ($meal) {
            return [
                'id' => $meal['idMeal'],
                'title' => $meal['strMeal'],
                'category' => $meal['strCategory'] ?? null,
                'image' => $meal['strMealThumb'] ?? null,
            ];
        });

        $categoriesResponse = Http::get("{$this->base}/list.php", ['c' => 'list']);
        $categories = collect($categoriesResponse->json('meals') ?? [])
            ->pluck('strCategory');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'meals' => $meals->values(),
            ]);
        }

        return view('discover.index', compact('meals', 'categories'));
    }

    public function show(string $id)
    {
        $response = Http::get("{$this->base}/lookup.php", ['i' => $id]);
        $meal = $response->json('meals')[0] ?? null;

        if (!$meal) {
            abort(404);
        }

        // Build ingredient list from MealDB's strIngredient1..20 / strMeasure1..20 fields
        $ingredients = [];
        for ($i = 1; $i <= 20; $i++) {
            $ingredient = $meal["strIngredient{$i}"] ?? null;
            $measure = $meal["strMeasure{$i}"] ?? null;
            if (!empty($ingredient)) {
                $ingredients[] = trim("{$measure} {$ingredient}");
            }
        }

        return view('discover.show', [
            'meal' => $meal,
            'ingredients' => $ingredients,
        ]);
    }
}