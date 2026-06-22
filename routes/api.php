<?php

Route::get('/recipes', function (Request $request) {

    return Recipe::query()
        ->when($request->search, function ($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%");
        })
        ->with('category')
        ->get();
});