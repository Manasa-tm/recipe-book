<x-app-layout>
    <!-- Hero -->
    <div class="bg-gradient-to-r from-red-600 via-red-500 to-orange-500 text-white">
        <div class="max-w-7xl mx-auto px-4 py-14 text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold mb-3">Find Your Next Favorite Recipe</h1>
            <p class="text-red-50 text-lg mb-2">Search, filter, and rate recipes shared by our community</p>
            @auth
                <a href="{{ route('recipes.create') }}"
                   class="inline-block mt-6 bg-white text-red-600 font-bold px-6 py-3 rounded-full shadow-lg hover:bg-red-50 transition">
                    + Share a Recipe
                </a>
            @endauth
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">

        <!-- ============================= -->
        <!-- Indian Recipes (TheMealDB API) -->
        <!-- ============================= -->
        <div class="mb-14">
            <div class="bg-white rounded-2xl shadow-md p-5 mb-6 -mt-16 relative z-10">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">🇮🇳</span>
                    <h2 class="text-xl font-bold text-gray-800">Indian Recipes</h2>
                    <span class="text-xs text-gray-400">powered by TheMealDB</span>
                </div>
                <div class="flex items-center bg-gray-50 rounded-full border border-gray-200 p-1">
                    <input type="text" id="indian-search"
                           class="flex-1 px-5 py-3 rounded-full bg-transparent text-gray-800 focus:outline-none"
                           placeholder="Search Indian dishes — chicken curry, dosa, biryani...">
                </div>
                <div id="indian-spinner" class="hidden text-sm text-gray-400 mt-2">Searching…</div>
            </div>

            <div id="indian-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="col-span-full text-center text-gray-400 py-10">Loading Indian recipes…</div>
            </div>
        </div>

        <!-- ============================= -->
        <!-- Your community recipes (DB)   -->
        <!-- ============================= -->
        <div class="bg-white rounded-2xl shadow-md p-5 mb-10 flex flex-col sm:flex-row gap-4">
            <input type="text" id="search-input"
                   class="flex-1 border border-gray-200 rounded-full px-5 py-3 focus:outline-none focus:ring-2 focus:ring-red-400"
                   placeholder="Search recipes..." value="{{ request('search') }}">

            <select id="category-select"
                    class="border border-gray-200 rounded-full px-5 py-3 focus:outline-none focus:ring-2 focus:ring-red-400">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>

            <div id="search-spinner" class="hidden self-center text-sm text-gray-400">Searching…</div>
        </div>

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">All Recipes</h2>
            <a href="{{ route('discover.index') }}" class="text-red-600 font-semibold hover:underline">
                Need ideas? Discover recipes →
            </a>
        </div>

        <!-- Recipe Grid -->
        <div id="recipe-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
            @forelse($recipes as $recipe)
                <div class="group bg-white rounded-2xl shadow-md overflow-hidden flex flex-col hover:shadow-xl transition transform hover:-translate-y-1">
                    <div class="h-52 overflow-hidden bg-gray-100">
                        @if($recipe->image)
                            <img src="{{ asset('storage/' . $recipe->image) }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                 alt="{{ $recipe->title }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl">🍽️</div>
                        @endif
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <span class="inline-block w-fit text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full mb-2">
                            {{ $recipe->category->name }}
                        </span>
                        <h3 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-red-600 transition">{{ $recipe->title }}</h3>
                        <p class="text-sm text-gray-500 mb-3">by {{ $recipe->user->name }}</p>
                        <p class="text-sm text-amber-500 font-semibold mb-4">⭐ {{ number_format($recipe->averageRating(), 1) }}</p>
                        <a href="{{ route('recipes.show', $recipe) }}"
                           class="mt-auto inline-block text-center bg-red-600 text-white px-4 py-2.5 rounded-full font-semibold hover:bg-red-700 transition">
                            View Recipe
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl shadow p-12 text-center">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No recipes found</h3>
                    <p class="text-gray-500 mb-4">Try a different search term, or explore recipes from around the world.</p>
                    <a href="{{ route('discover.index') }}" class="text-red-600 font-semibold hover:underline">
                        Discover recipes →
                    </a>
                </div>
            @endforelse
        </div>

        <div id="pagination-wrap" class="mt-10">
            {{ $recipes->withQueryString()->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            // ===== Indian Recipes (TheMealDB, area=Indian) =====
            const indianSearch = document.getElementById('indian-search');
            const indianGrid = document.getElementById('indian-grid');
            const indianSpinner = document.getElementById('indian-spinner');
            let indianDebounce = null;

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str ?? '';
                return div.innerHTML;
            }

            function indianCardTemplate(meal) {
                const imageHtml = meal.image
                    ? `<div class="h-44 overflow-hidden"><img src="${meal.image}" alt="${escapeHtml(meal.title)}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300"></div>`
                    : '';
                const categoryHtml = meal.category
                    ? `<span class="inline-block text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full mb-1">${escapeHtml(meal.category)}</span>`
                    : '';
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

            function indianEmptyTemplate() {
                return `
                    <div class="col-span-full bg-white rounded-2xl shadow p-10 text-center">
                        <h3 class="text-lg font-bold text-gray-700 mb-1">No Indian recipes found</h3>
                        <p class="text-gray-500">Try a different search term.</p>
                    </div>
                `;
            }

            async function fetchIndianRecipes() {
                indianSpinner.classList.remove('hidden');
                try {
                    const params = new URLSearchParams();
                    if (indianSearch.value.trim()) params.set('search', indianSearch.value.trim());

                    const response = await fetch(`{{ route('indian-recipes.search') }}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    });
                    const data = await response.json();

                    indianGrid.innerHTML = (!data.meals || data.meals.length === 0)
                        ? indianEmptyTemplate()
                        : data.meals.slice(0, 12).map(indianCardTemplate).join('');
                } catch (err) {
                    console.error('Indian recipe search failed:', err);
                    indianGrid.innerHTML = indianEmptyTemplate();
                } finally {
                    indianSpinner.classList.add('hidden');
                }
            }

            indianSearch.addEventListener('input', function () {
                clearTimeout(indianDebounce);
                indianDebounce = setTimeout(fetchIndianRecipes, 400);
            });

            // Initial load
            fetchIndianRecipes();

            // ===== Local DB recipes (existing behavior, unchanged) =====
            const searchInput   = document.getElementById('search-input');
            const categorySelect = document.getElementById('category-select');
            const grid           = document.getElementById('recipe-grid');
            const paginationWrap = document.getElementById('pagination-wrap');
            const spinner        = document.getElementById('search-spinner');

            let debounceTimer = null;

            function cardTemplate(recipe) {
                const imageHtml = recipe.image_url
                    ? `<img src="${recipe.image_url}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="${escapeHtml(recipe.title)}">`
                    : `<div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl">🍽️</div>`;

                return `
                    <div class="group bg-white rounded-2xl shadow-md overflow-hidden flex flex-col hover:shadow-xl transition transform hover:-translate-y-1">
                        <div class="h-52 overflow-hidden bg-gray-100">${imageHtml}</div>
                        <div class="p-5 flex flex-col flex-1">
                            <span class="inline-block w-fit text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full mb-2">${escapeHtml(recipe.category)}</span>
                            <h3 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-red-600 transition">${escapeHtml(recipe.title)}</h3>
                            <p class="text-sm text-gray-500 mb-3">by ${escapeHtml(recipe.user)}</p>
                            <p class="text-sm text-amber-500 font-semibold mb-4">⭐ ${escapeHtml(recipe.average_rating)}</p>
                            <a href="${recipe.show_url}" class="mt-auto inline-block text-center bg-red-600 text-white px-4 py-2.5 rounded-full font-semibold hover:bg-red-700 transition">View Recipe</a>
                        </div>
                    </div>
                `;
            }

            function emptyTemplate() {
                return `
                    <div class="col-span-full bg-white rounded-2xl shadow p-12 text-center">
                        <div class="text-5xl mb-4">🔍</div>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">No recipes found</h3>
                        <p class="text-gray-500 mb-4">Try a different search term, or explore recipes from around the world.</p>
                        <a href="{{ route('discover.index') }}" class="text-red-600 font-semibold hover:underline">Discover recipes →</a>
                    </div>
                `;
            }

            function buildParams() {
                const params = new URLSearchParams();
                const search = searchInput.value.trim();
                const category = categorySelect.value;
                if (search) params.set('search', search);
                if (category) params.set('category', category);
                return params;
            }

            function updateUrl(params) {
                const qs = params.toString();
                const newUrl = qs ? `${window.location.pathname}?${qs}` : window.location.pathname;
                window.history.pushState({}, '', newUrl);
            }

            async function fetchRecipes() {
                const params = buildParams();
                updateUrl(params);
                spinner.classList.remove('hidden');

                try {
                    const response = await fetch(`{{ route('recipes.index') }}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    });
                    if (!response.ok) throw new Error('Request failed: ' + response.status);
                    const data = await response.json();

                    grid.innerHTML = (!data.recipes || data.recipes.length === 0)
                        ? emptyTemplate()
                        : data.recipes.map(cardTemplate).join('');

                    paginationWrap.innerHTML = data.pagination?.links ?? '';
                } catch (err) {
                    console.error('Search failed:', err);
                } finally {
                    spinner.classList.add('hidden');
                }
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchRecipes, 400);
            });

            categorySelect.addEventListener('change', fetchRecipes);

            paginationWrap.addEventListener('click', function (e) {
                const link = e.target.closest('a');
                if (!link) return;
                e.preventDefault();

                const url = new URL(link.href);
                const pageParams = new URLSearchParams(url.search);
                if (searchInput.value.trim()) pageParams.set('search', searchInput.value.trim());
                if (categorySelect.value) pageParams.set('category', categorySelect.value);

                window.history.pushState({}, '', `${window.location.pathname}?${pageParams.toString()}`);

                fetch(`{{ route('recipes.index') }}?${pageParams.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                    .then(r => r.json())
                    .then(data => {
                        grid.innerHTML = (!data.recipes || data.recipes.length === 0)
                            ? emptyTemplate()
                            : data.recipes.map(cardTemplate).join('');
                        paginationWrap.innerHTML = data.pagination?.links ?? '';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    })
                    .catch(err => console.error('Pagination fetch failed:', err));
            });

            window.addEventListener('popstate', function () {
                const params = new URLSearchParams(window.location.search);
                searchInput.value = params.get('search') || '';
                categorySelect.value = params.get('category') || '';
                fetchRecipes();
            });
        })();
    </script>
    @endpush
</x-app-layout>