<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Recipe</h2>
        <form method="POST" action="{{ route('recipes.update', $recipe) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('recipes.partials._form')
        </form>
    </div>
</x-app-layout>