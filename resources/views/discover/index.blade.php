<x-app-layout>
    <!-- Hero -->
    <div class="bg-gradient-to-r from-red-600 to-orange-500 text-white">
        <div class="max-w-7xl mx-auto px-4 py-12 text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold mb-3">Discover New Recipes</h1>
            <p class="text-red-50 text-lg mb-6">Powered by TheMealDB — search thousands of recipes from around the world</p>
            @auth
            <a href="{{ route('recipes.create') }}"
                class="inline-block mb-8 bg-white text-red-600 font-bold px-6 py-3 rounded-full shadow-lg hover:bg-red-50 transition">
                + Share a Recipe
            </a>
            @endauth

            <div class="max-w-4xl mx-auto bg-white rounded-full shadow-lg flex items-center p-2">
                <input type="text" id="discover-search"
                    class="flex-1 px-5 py-3 rounded-full text-gray-800 focus:outline-none"
                    placeholder="Search for chicken, pasta, dessert...">
                <select id="discover-category"
                    class="px-6 py-3 text-gray-700 border-l border-gray-200 focus:outline-none" style="border-radius: 20px;">
                    <option value="">All Categories </option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <style>
        @media (max-width: 768px) {
            .discover-category {
                width: 100%;
                border-left: none !important;
                border-top: 1px solid #e5e7eb;
                padding: 12px 16px !important;
                text-align: center;
                text-align-last: center;
                /* Centers selected option text */
                margin-top: 10px;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <div id="discover-spinner" class="hidden text-center text-gray-400 mb-6">Loading recipes…</div>

        <div id="discover-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($meals as $meal)
            <a href="{{ route('discover.show', $meal['id']) }}"
                class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1">
                @if($meal['image'])
                <div class="h-48 overflow-hidden">
                    <img src="{{ $meal['image'] }}" alt="{{ $meal['title'] }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                @endif
                <div class="p-4">
                    @if($meal['category'])
                    <span class="inline-block text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full mb-2">
                        {{ $meal['category'] }}
                    </span>
                    @endif
                    <h3 class="font-bold text-gray-800 leading-snug group-hover:text-red-600 transition">
                        {{ $meal['title'] }}
                    </h3>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center bg-white rounded-2xl shadow p-10">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No recipes found</h3>
                <p class="text-gray-500">Try a different search term or category.</p>
            </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            const searchInput = document.getElementById('discover-search');
            const categorySelect = document.getElementById('discover-category');
            const grid = document.getElementById('discover-grid');
            const spinner = document.getElementById('discover-spinner');
            let debounceTimer = null;

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str ?? '';
                return div.innerHTML;
            }

            function cardTemplate(meal) {
                const imageHtml = meal.image ?
                    `<div class="h-48 overflow-hidden"><img src="${meal.image}" alt="${escapeHtml(meal.title)}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300"></div>` :
                    '';
                const categoryHtml = meal.category ?
                    `<span class="inline-block text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full mb-2">${escapeHtml(meal.category)}</span>` :
                    '';

                return `
                    <a href="/discover/${meal.id}" class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1">
                        ${imageHtml}
                        <div class="p-4">
                            ${categoryHtml}
                            <h3 class="font-bold text-gray-800 leading-snug group-hover:text-red-600 transition">${escapeHtml(meal.title)}</h3>
                        </div>
                    </a>
                `;
            }

            function emptyTemplate() {
                return `
                    <div class="col-span-full text-center bg-white rounded-2xl shadow p-10">
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No recipes found</h3>
                        <p class="text-gray-500">Try a different search term or category.</p>
                    </div>
                `;
            }

            async function fetchMeals() {
                const params = new URLSearchParams();
                if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
                if (categorySelect.value) params.set('category', categorySelect.value);

                spinner.classList.remove('hidden');

                try {
                    const response = await fetch(`{{ route('discover.index') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                    });
                    const data = await response.json();

                    grid.innerHTML = (!data.meals || data.meals.length === 0) ?
                        emptyTemplate() :
                        data.meals.map(cardTemplate).join('');
                } catch (err) {
                    console.error('Discover search failed:', err);
                } finally {
                    spinner.classList.add('hidden');
                }
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchMeals, 400);
            });

            categorySelect.addEventListener('change', fetchMeals);
        })();
    </script>
    @endpush
</x-app-layout>