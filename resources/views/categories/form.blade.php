<x-layouts.dashboard>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isset($category) ? 'Edit Category' : 'Create Category' }}
            </h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400">{{ isset($category) ? 'Update category details' : 'Add a new category to organize your products' }}</p>
    </div>

    <form action="{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="max-w-4xl">
        @csrf
        @if(isset($category))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Category Name *</label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name', $category->name ?? '') }}"
                                required
                                class="input w-full"
                                placeholder="e.g., Electronics, Clothing, Food">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Slug</label>
                            <input 
                                type="text" 
                                name="slug" 
                                value="{{ old('slug', $category->slug ?? '') }}"
                                class="input w-full"
                                placeholder="auto-generated-from-name">
                            <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate from name</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Description</label>
                            <textarea 
                                name="description" 
                                rows="3"
                                class="input w-full"
                                placeholder="Brief description of this category...">{{ old('description', $category->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Hierarchy -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Hierarchy</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Parent Category</label>
                            <select name="parent_id" class="input w-full">
                                <option value="">None (Top Level)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" 
                                        {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}
                                        {{ isset($category) && $parent->id == $category->id ? 'disabled' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select a parent to make this a subcategory</p>
                            @error('parent_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Sort Order</label>
                            <input 
                                type="number" 
                                name="sort_order" 
                                min="0"
                                value="{{ old('sort_order', $category->sort_order ?? '0') }}"
                                class="input w-full"
                                placeholder="0">
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO & Metadata -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">SEO & Metadata</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Meta Title</label>
                            <input 
                                type="text" 
                                name="meta_title" 
                                value="{{ old('meta_title', $category->meta_title ?? '') }}"
                                class="input w-full"
                                placeholder="SEO title for search engines">
                            <p class="mt-1 text-xs text-gray-500">Recommended: 50-60 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Meta Description</label>
                            <textarea 
                                name="meta_description" 
                                rows="2"
                                class="input w-full"
                                placeholder="SEO description for search engines...">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Recommended: 150-160 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Meta Keywords</label>
                            <input 
                                type="text" 
                                name="meta_keywords" 
                                value="{{ old('meta_keywords', $category->meta_keywords ?? '') }}"
                                class="input w-full"
                                placeholder="keyword1, keyword2, keyword3">
                            <p class="mt-1 text-xs text-gray-500">Comma-separated keywords</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Status</h3>
                    <select name="is_active" class="input w-full">
                        <option value="1" {{ old('is_active', $category->is_active ?? true) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $category->is_active ?? true) ? '' : 'selected' }}>Inactive</option>
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Inactive categories are hidden from the storefront</p>
                </div>

                <!-- Category Image -->
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Category Image</h3>
                    
                    <div x-data="{ preview: '{{ isset($category) && $category->image_url ? $category->image_url : '' }}' }">
                        <!-- Image Preview -->
                        <div x-show="preview" class="mb-3">
                            <div class="aspect-video bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                <img :src="preview" alt="Preview" class="w-full h-full object-cover">
                            </div>
                            <button 
                                type="button"
                                @click="preview = ''"
                                class="mt-2 text-sm text-red-600 hover:text-red-800">
                                Remove Image
                            </button>
                        </div>

                        <!-- Upload Button -->
                        <label class="block cursor-pointer">
                            <input 
                                type="file" 
                                name="image"
                                accept="image/*"
                                @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }"
                                class="hidden">
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-brand.primary transition-colors">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload</p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                            </div>
                        </label>
                    </div>
                    @error('image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Featured -->
                <div class="card">
                    <div class="flex items-start gap-3">
                        <input 
                            type="checkbox" 
                            name="is_featured" 
                            id="is_featured"
                            class="checkbox mt-1"
                            {{ old('is_featured', $category->is_featured ?? false) ? 'checked' : '' }}>
                        <div>
                            <label for="is_featured" class="text-sm font-medium dark:text-white">Featured Category</label>
                            <p class="text-xs text-gray-500 mt-1">Display on homepage and special sections</p>
                        </div>
                    </div>
                </div>

                <!-- Stats (Edit Only) -->
                @if(isset($category))
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Statistics</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Products:</span>
                            <span class="font-medium dark:text-white">{{ $category->products_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Subcategories:</span>
                            <span class="font-medium dark:text-white">{{ $category->children_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Created:</span>
                            <span class="font-medium dark:text-white">{{ $category->created_at?->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Save Actions -->
                <div class="card">
                    <div class="space-y-2">
                        <button type="submit" class="btn-primary w-full">
                            {{ isset($category) ? 'Update Category' : 'Create Category' }}
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn-outline w-full block text-center">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-layouts.dashboard>
