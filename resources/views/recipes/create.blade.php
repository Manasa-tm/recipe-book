<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Add New Recipe</h2>
        <form method="POST" action="{{ route('recipes.store') }}" enctype="multipart/form-data">
            @include('recipes.partials._form')
        </form>
    </div>
</x-app-layout>