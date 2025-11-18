<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Product Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Product Images -->
                        <div>
                            @if($product->media->first())
                                <img 
                                    src="{{ $product->media->first()->url }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-96 object-cover rounded-lg"
                                >
                            @else
                                <div class="w-full h-96 bg-gray-200 flex items-center justify-center rounded-lg">
                                    <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                            
                            @if($product->brand)
                                <p class="text-gray-600 mb-4">by {{ $product->brand->name }}</p>
                            @endif

                            @if($product->category)
                                <p class="text-sm text-gray-500 mb-4">
                                    Category: <a href="{{ route('categories.show', $product->category->slug) }}" class="text-indigo-600 hover:underline">{{ $product->category->name }}</a>
                                </p>
                            @endif

                            <!-- Price -->
                            <div class="mb-6">
                                <p class="text-4xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</p>
                                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <p class="text-lg text-gray-500 line-through">${{ number_format($product->compare_at_price, 2) }}</p>
                                    <p class="text-sm text-green-600">Save {{ number_format((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100, 0) }}%</p>
                                @endif
                            </div>

                            <!-- Rating -->
                            @if($product->average_rating)
                                <div class="flex items-center mb-6">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-5 w-5 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-gray-600">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews_count }} reviews)</span>
                                </div>
                            @endif

                            <!-- Stock Status -->
                            @if($product->stock_quantity !== null)
                                @if($product->stock_quantity > 0)
                                    <p class="text-green-600 font-semibold mb-6">In Stock ({{ $product->stock_quantity }} available)</p>
                                @else
                                    <p class="text-red-600 font-semibold mb-6">Out of Stock</p>
                                @endif
                            @endif

                            <!-- Description -->
                            @if($product->description)
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-2">Description</h3>
                                    <p class="text-gray-700">{{ $product->description }}</p>
                                </div>
                            @endif

                            <!-- Add to Cart Button (placeholder) -->
                            <button class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>
                    
                    @forelse($product->approvedReviews as $review)
                        <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0">
                            <div class="flex items-center mb-2">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                @if($review->is_verified_purchase)
                                    <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Verified Purchase</span>
                                @endif
                            </div>
                            
                            @if($review->title)
                                <h4 class="font-semibold mb-1">{{ $review->title }}</h4>
                            @endif
                            
                            <p class="text-gray-700 mb-2">{{ $review->comment }}</p>
                            <p class="text-sm text-gray-500">
                                By {{ $review->user->name }} on {{ $review->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
                    @endforelse
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold mb-6">Related Products</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($relatedProducts as $relatedProduct)
                                <x-product-card :product="$relatedProduct" />
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
