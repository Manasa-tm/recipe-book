<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">

        @if(session('success'))
            <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-1">{{ $recipe->title }}</h2>
                    <p class="text-gray-500">
                        {{ $recipe->category->name }} · by {{ $recipe->user->name }} · {{ $recipe->cook_time_minutes }} mins
                    </p>
                </div>

                @auth
                    @if($recipe->user_id === auth()->id())
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ route('recipes.edit', $recipe) }}"
                               class="bg-amber-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-amber-600 transition">
                                Edit
                            </a>
                            <form action="{{ route('recipes.destroy', $recipe) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Delete this recipe?')"
                                        class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-red-700 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            @if($recipe->image)
                <img src="{{ asset('storage/' . $recipe->image) }}"
                     class="w-full max-h-[400px] object-cover rounded-lg mb-6" alt="{{ $recipe->title }}">
            @endif

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Description</h3>
            <p class="text-gray-700">{{ $recipe->description }}</p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Ingredients</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                @foreach(explode("\n", $recipe->ingredients) as $ingredient)
                    <li>{{ trim($ingredient) }}</li>
                @endforeach
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Steps</h3>
            <p class="text-gray-700 whitespace-pre-line">{{ $recipe->steps }}</p>

            <h3 class="text-xl font-semibold text-gray-800 mt-8 mb-2">
                Average Rating: <span class="text-amber-500">⭐ {{ number_format($recipe->averageRating(), 1) }}</span>
            </h3>

            @auth
                <form action="{{ route('ratings.store', $recipe) }}" method="POST" class="flex items-center gap-3 mt-4">
                    @csrf
                    <select name="stars"
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Rate
                    </button>
                </form>
            @endauth
        </div>
    </div>
</x-app-layout>