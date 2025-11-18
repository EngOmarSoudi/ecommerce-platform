<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-8">
                @foreach($categories as $category)
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium flex items-center">
                            {{ $category->name }}
                            @if($category->children->count() > 0)
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            @endif
                        </button>
                        
                        @if($category->children->count() > 0)
                            <div class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1" role="menu" aria-orientation="vertical">
                                    <a href="{{ route('categories.show', $category->slug) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        All {{ $category->name }}
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    @foreach($category->children as $subcategory)
                                        <a href="{{ route('categories.show', $subcategory->slug) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                            {{ $subcategory->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</nav>