<x-layouts.dashboard>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isset($product) ? 'Edit Product' : 'Create Product' }}
            </h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400">{{ isset($product) ? 'Update product details' : 'Add a new product to your catalog' }}</p>
    </div>

    <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          x-data="productForm()"
          @submit="return validateForm($event)">
        @csrf
        @if(isset($product))
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
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Product Name *</label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name', $product->name ?? '') }}"
                                required
                                class="input w-full"
                                placeholder="e.g., Premium Cotton T-Shirt">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Description</label>
                            <textarea 
                                name="description" 
                                rows="4"
                                class="input w-full"
                                placeholder="Detailed product description...">{{ old('description', $product->description ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">SKU</label>
                                <input 
                                    type="text" 
                                    name="sku" 
                                    value="{{ old('sku', $product->sku ?? '') }}"
                                    class="input w-full"
                                    placeholder="AUTO">
                                <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Barcode</label>
                                <input 
                                    type="text" 
                                    name="barcode" 
                                    value="{{ old('barcode', $product->barcode ?? '') }}"
                                    class="input w-full"
                                    placeholder="Enter barcode">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Pricing</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Price *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                <input 
                                    type="number" 
                                    name="price" 
                                    step="0.01"
                                    min="0"
                                    value="{{ old('price', $product->price ?? '') }}"
                                    required
                                    class="input w-full pl-8"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Compare at Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                <input 
                                    type="number" 
                                    name="compare_price" 
                                    step="0.01"
                                    min="0"
                                    value="{{ old('compare_price', $product->compare_price ?? '') }}"
                                    class="input w-full pl-8"
                                    placeholder="0.00">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Original price for sale badges</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Cost per Item</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                <input 
                                    type="number" 
                                    name="cost" 
                                    step="0.01"
                                    min="0"
                                    value="{{ old('cost', $product->cost ?? '') }}"
                                    class="input w-full pl-8"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Tax Rate (%)</label>
                            <input 
                                type="number" 
                                name="tax_rate" 
                                step="0.01"
                                min="0"
                                max="100"
                                value="{{ old('tax_rate', $product->tax_rate ?? '10') }}"
                                class="input w-full"
                                placeholder="10.00">
                        </div>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Inventory</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Stock Quantity</label>
                                <input 
                                    type="number" 
                                    name="stock_quantity" 
                                    min="0"
                                    value="{{ old('stock_quantity', $product->stock_quantity ?? '0') }}"
                                    class="input w-full"
                                    placeholder="0">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Low Stock Alert</label>
                                <input 
                                    type="number" 
                                    name="low_stock_threshold" 
                                    min="0"
                                    value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? '10') }}"
                                    class="input w-full"
                                    placeholder="10">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Unit of Measurement</label>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" @click="selectUnit('piece')" :class="selectedUnit === 'piece' ? 'bg-brand.primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="py-2 px-4 rounded-lg font-medium transition-colors">Piece</button>
                                <button type="button" @click="selectUnit('box')" :class="selectedUnit === 'box' ? 'bg-brand.primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="py-2 px-4 rounded-lg font-medium transition-colors">Box</button>
                                <button type="button" @click="selectUnit('dozen')" :class="selectedUnit === 'dozen' ? 'bg-brand.primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="py-2 px-4 rounded-lg font-medium transition-colors">Dozen</button>
                            </div>
                            <div class="mt-3 flex gap-2">
                                <input 
                                    type="text" 
                                    x-model="customUnit"
                                    @input="selectUnit(customUnit)"
                                    class="input flex-1"
                                    placeholder="Or enter custom unit...">
                            </div>
                            <input type="hidden" name="unit" x-model="selectedUnit">
                        </div>

                        <div class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                name="track_inventory" 
                                id="track_inventory"
                                class="checkbox"
                                {{ old('track_inventory', $product->track_inventory ?? true) ? 'checked' : '' }}>
                            <label for="track_inventory" class="text-sm dark:text-gray-300">Track inventory for this product</label>
                        </div>
                    </div>
                </div>

                <!-- SKU Variants Builder -->
                <div class="card" x-show="showVariants">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold dark:text-white">Product Variants</h2>
                        <button type="button" @click="showVariants = false" class="text-sm text-gray-500 hover:text-gray-700">Remove Variants</button>
                    </div>

                    <div class="space-y-4">
                        <!-- Variant Options -->
                        <div class="border dark:border-gray-700 rounded-lg p-4">
                            <div class="space-y-3">
                                <div x-show="!variants.colors.length && !variants.sizes.length" class="text-center py-4">
                                    <p class="text-sm text-gray-500 mb-3">Add variant options like colors, sizes, or materials</p>
                                    <div class="flex gap-2 justify-center">
                                        <button type="button" @click="addVariantType('colors')" class="btn-outline text-sm">+ Add Colors</button>
                                        <button type="button" @click="addVariantType('sizes')" class="btn-outline text-sm">+ Add Sizes</button>
                                        <button type="button" @click="addVariantType('materials')" class="btn-outline text-sm">+ Add Materials</button>
                                    </div>
                                </div>

                                <template x-for="(type, key) in variants" :key="key">
                                    <div x-show="type.length > 0" class="border dark:border-gray-600 rounded p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="text-sm font-medium dark:text-gray-300 capitalize" x-text="key"></label>
                                            <button type="button" @click="removeVariantType(key)" class="text-xs text-red-600 hover:text-red-800">Remove</button>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            <template x-for="(value, index) in type" :key="index">
                                                <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full text-sm">
                                                    <span x-text="value"></span>
                                                    <button type="button" @click="removeVariantValue(key, index)" class="text-gray-500 hover:text-red-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </span>
                                            </template>
                                        </div>
                                        <input 
                                            type="text" 
                                            @keydown.enter.prevent="addVariantValue(key, $event.target.value); $event.target.value = ''"
                                            class="input w-full text-sm"
                                            :placeholder="'Add ' + key + ' (press Enter)'">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Generated Variants Table -->
                        <div x-show="generatedVariants.length > 0">
                            <h3 class="text-sm font-medium mb-2 dark:text-gray-300">Generated Variants (<span x-text="generatedVariants.length"></span>)</h3>
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Variant</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-for="(variant, index) in generatedVariants" :key="index">
                                            <tr>
                                                <td class="px-4 py-2 text-sm" x-text="variant.name"></td>
                                                <td class="px-4 py-2">
                                                    <input type="text" x-model="variant.sku" class="input text-sm w-32">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" step="0.01" x-model="variant.price" class="input text-sm w-24">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" x-model="variant.stock" class="input text-sm w-20">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Show Variants Button -->
                <div x-show="!showVariants">
                    <button type="button" @click="showVariants = true; addVariantType('colors')" class="btn-outline w-full">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Variants (Colors, Sizes, etc.)
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Product Status</h3>
                    <select name="is_active" class="input w-full">
                        <option value="1" {{ old('is_active', $product->is_active ?? true) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $product->is_active ?? true) ? '' : 'selected' }}>Inactive</option>
                    </select>
                </div>

                <!-- Category -->
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Category</h3>
                    <select name="category_id" class="input w-full">
                        <option value="">Select Category</option>
                        @foreach(\App\Models\Category::whereNull('parent_id')->get() as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @foreach($cat->children as $subcat)
                                <option value="{{ $subcat->id }}" {{ old('category_id', $product->category_id ?? '') == $subcat->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;→ {{ $subcat->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <!-- Product Images -->
                <div class="card">
                    <h3 class="text-sm font-semibold mb-3 dark:text-white">Product Images</h3>
                    
                    <div class="space-y-3">
                        <!-- Image Preview Grid -->
                        <div class="grid grid-cols-2 gap-2" id="imagePreviewGrid">
                            <template x-for="(image, index) in images" :key="index">
                                <div class="relative group aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                    <img :src="image.url" class="w-full h-full object-cover">
                                    <button 
                                        type="button"
                                        @click="removeImage(index)"
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div x-show="index === 0" class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs py-1 text-center">
                                        Primary
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Upload Button -->
                        <label class="block">
                            <input 
                                type="file" 
                                name="images[]"
                                multiple
                                accept="image/*"
                                @change="previewImages($event)"
                                class="hidden"
                                id="imageInput">
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-brand.primary transition-colors">
                                <svg class="w-10 h-10 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload images</p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="card">
                    <div class="space-y-2">
                        <button type="submit" class="btn-primary w-full">
                            {{ isset($product) ? 'Update Product' : 'Create Product' }}
                        </button>
                        <a href="{{ route('products.index') }}" class="btn-outline w-full block text-center">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function productForm() {
            return {
                selectedUnit: '{{ old("unit", $product->unit ?? "piece") }}',
                customUnit: '',
                showVariants: false,
                variants: {
                    colors: [],
                    sizes: [],
                    materials: []
                },
                generatedVariants: [],
                images: @json(isset($product) && $product->images ? $product->images : []),

                selectUnit(unit) {
                    this.selectedUnit = unit;
                },

                addVariantType(type) {
                    if (!this.variants[type]) {
                        this.variants[type] = [];
                    }
                },

                removeVariantType(type) {
                    this.variants[type] = [];
                    this.generateVariants();
                },

                addVariantValue(type, value) {
                    if (value && !this.variants[type].includes(value)) {
                        this.variants[type].push(value);
                        this.generateVariants();
                    }
                },

                removeVariantValue(type, index) {
                    this.variants[type].splice(index, 1);
                    this.generateVariants();
                },

                generateVariants() {
                    const combinations = [];
                    const types = Object.keys(this.variants).filter(k => this.variants[k].length > 0);
                    
                    if (types.length === 0) {
                        this.generatedVariants = [];
                        return;
                    }

                    const generate = (current, depth) => {
                        if (depth === types.length) {
                            combinations.push([...current]);
                            return;
                        }
                        
                        const type = types[depth];
                        this.variants[type].forEach(value => {
                            generate([...current, { type, value }], depth + 1);
                        });
                    };

                    generate([], 0);

                    this.generatedVariants = combinations.map((combo, index) => {
                        const name = combo.map(c => c.value).join(' / ');
                        const skuSuffix = combo.map(c => c.value.substring(0, 3).toUpperCase()).join('-');
                        return {
                            name,
                            sku: `VAR-${skuSuffix}-${index + 1}`,
                            price: 0,
                            stock: 0
                        };
                    });
                },

                previewImages(event) {
                    const files = event.target.files;
                    Array.from(files).forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.images.push({ url: e.target.result, file });
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                },

                removeImage(index) {
                    this.images.splice(index, 1);
                },

                validateForm(event) {
                    // Add custom validation if needed
                    return true;
                }
            }
        }
    </script>
    @endpush
</x-layouts.dashboard>
