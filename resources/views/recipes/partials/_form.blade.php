@csrf

<div class="bg-white rounded-xl shadow-md p-6 space-y-5">

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 border border-red-300 rounded-lg px-4 py-3">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input type="text" name="title" id="title"
               value="{{ old('title', $recipe->title ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>

    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
        <select name="category_id" id="category_id"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Select a category</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    @selected(old('category_id', $recipe->category_id ?? '') == $cat->id)>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="cook_time_minutes" class="block text-sm font-medium text-gray-700 mb-1">Cook Time (minutes)</label>
        <input type="number" name="cook_time_minutes" id="cook_time_minutes" min="1"
               value="{{ old('cook_time_minutes', $recipe->cook_time_minutes ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea name="description" id="description" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('description', $recipe->description ?? '') }}</textarea>
    </div>

    <div>
        <label for="ingredients" class="block text-sm font-medium text-gray-700 mb-1">
            Ingredients <span class="text-gray-400 font-normal">(one per line)</span>
        </label>
        <textarea name="ingredients" id="ingredients" rows="5"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('ingredients', $recipe->ingredients ?? '') }}</textarea>
    </div>

    <div>
        <label for="steps" class="block text-sm font-medium text-gray-700 mb-1">Steps</label>
        <textarea name="steps" id="steps" rows="6"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('steps', $recipe->steps ?? '') }}</textarea>
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
        @if(!empty($recipe->image))
            <img src="{{ asset('storage/' . $recipe->image) }}"
                 class="w-40 h-28 object-cover rounded-lg mb-2" alt="Current image">
        @endif
        <input type="file" name="image" id="image"
               class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
    </div>

    <div class="pt-2">
        <button type="submit"
                class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
            Save Recipe
        </button>
        <a href="{{ route('recipes.index') }}"
           class="ml-2 text-gray-600 hover:text-gray-800">
            Cancel
        </a>
    </div>
</div>