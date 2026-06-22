<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class RecipeController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('recipes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'ingredients' => 'required',
            'steps' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'cook_time_minutes' => 'nullable|integer',
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('recipes', 'public');
        }

        Recipe::create($validated);

        return redirect()->route('recipes.index')->with('success', 'Recipe added!');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['user', 'category', 'ratings.user']);
        return view('recipes.show', compact('recipe'));
    }

    public function edit(Recipe $recipe)
    {
        $this->authorizeOwner($recipe);
        $categories = Category::all();
        return view('recipes.edit', compact('recipe', 'categories'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $this->authorizeOwner($recipe);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'ingredients' => 'required',
            'steps' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'cook_time_minutes' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            if ($recipe->image) {
                Storage::disk('public')->delete($recipe->image);
            }
            $validated['image'] = $request->file('image')->store('recipes', 'public');
        }

        $recipe->update($validated);

        return redirect()->route('recipes.show', $recipe)->with('success', 'Recipe updated!');
    }

    public function destroy(Recipe $recipe)
    {
        $this->authorizeOwner($recipe);

        if ($recipe->image) {
            Storage::disk('public')->delete($recipe->image);
        }

        $recipe->delete();

        return redirect()->route('recipes.index')->with('success', 'Recipe deleted!');
    }

    private function authorizeOwner(Recipe $recipe)
    {
        if ($recipe->user_id !== Auth::id()) {
            abort(403);
        }
    }
}