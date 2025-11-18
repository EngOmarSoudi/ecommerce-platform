<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $category->name }}
                </h2>
                @if($category->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Subcategories (if any) -->
            @if($category->children && $category->children->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Browse Subcategories</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($category->children as $subcategory)
                                <a 
                                    href="{{ route('categories.show', $subcategory->slug) }}" 
                                    class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-center"
                                >
                                    <p class="font-medium text-gray-900">{{ $subcategory->name }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filters and Sorting -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('categories.show', $category->slug) }}" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select 
                                name="sort_by" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                onchange="this.form.submit()"
                            >
                                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Newest</option>
                                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                                <option value="price" {{ request('sort_by') === 'price' ? 'selected' : '' }}>Price: Low to High</option>
                            </select>
                        </div>
                        
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                            <select 
                                name="per_page" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                onchange="this.form.submit()"
                            >
                                <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                                <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-gray-600">
                            Showing <span class="font-semibold">{{ $products->firstItem() ?? 0 }}</span> 
                            to <span class="font-semibold">{{ $products->lastItem() ?? 0 }}</span> 
                            of <span class="font-semibold">{{ $products->total() }}</span> products
                        </p>
                    </div>
                    
                    <x-product-grid :products="$products" />
                    
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
