<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <a href="{{ route('discover.index') }}" class="text-red-600 font-medium hover:underline mb-4 inline-block">
            ← Back to Discover
        </a>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            @if($meal['strMealThumb'])
                <img src="{{ $meal['strMealThumb'] }}" alt="{{ $meal['strMeal'] }}"
                     class="w-full h-80 object-cover">
            @endif

            <div class="p-6">
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($meal['strCategory'])
                        <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-3 py-1 rounded-full">
                            {{ $meal['strCategory'] }}
                        </span>
                    @endif
                    @if($meal['strArea'])
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-3 py-1 rounded-full">
                            {{ $meal['strArea'] }} Cuisine
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl font-extrabold text-gray-800 mb-6">{{ $meal['strMeal'] }}</h1>

                <h2 class="text-xl font-bold text-gray-800 mb-3">Ingredients</h2>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-8">
                    @foreach($ingredients as $ingredient)
                        <li class="flex items-center text-gray-700">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2 shrink-0"></span>
                            {{ $ingredient }}
                        </li>
                    @endforeach
                </ul>

                <h2 class="text-xl font-bold text-gray-800 mb-3">Instructions</h2>
                <p class="text-gray-700 whitespace-pre-line leading-relaxed mb-6">{{ $meal['strInstructions'] }}</p>

                @if($meal['strYoutube'])
                    <a href="{{ $meal['strYoutube'] }}" target="_blank"
                       class="inline-block bg-red-600 text-white px-5 py-2.5 rounded-full font-semibold hover:bg-red-700 transition">
                        ▶ Watch on YouTube
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>