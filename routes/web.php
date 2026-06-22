<?php

use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\IndianRecipeController;


Route::get('/discover', [DiscoverController::class, 'index'])->name('discover.index');
Route::get('/discover/{id}', [DiscoverController::class, 'show'])->name('discover.show');
Route::get('/api/indian-recipes', [IndianRecipeController::class, 'search'])->name('indian-recipes.search');

Route::get('/', [DiscoverController::class, 'index'])->name('home');

Route::resource('recipes', RecipeController::class)->except(['index']);

Route::middleware('auth')->group(function () {
    Route::post('/recipes/{recipe}/rate', [RatingController::class, 'store'])->name('ratings.store');

    Route::get('/dashboard', function () {
        $recipes = auth()->user()->recipes()->latest()->paginate(6);
        return view('dashboard', compact('recipes'));
    })->name('dashboard');

    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';