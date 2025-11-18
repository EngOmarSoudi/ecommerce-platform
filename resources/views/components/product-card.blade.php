@props(['product'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200">
            @if($product->media->first())
                <img src="{{ $product->media->first()->url }}" alt="{{ $product->name }}" class="h-48 w-full object-cover object-center">
            @else
                <div class="h-48 w-full flex items-center justify-center bg-gray-100">
                    <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
        </div>
        
        <div class="p-4">
            <h3 class="text-sm font-medium text-gray-900 line-clamp-2 mb-2">{{ $product->name }}</h3>
            
            @if($product->brand)
                <p class="text-xs text-gray-500 mb-2">{{ $product->brand->name }}</p>
            @endif
            
            <div class="flex items-center justify-between">
                <div>
                    @if($product->price)
                        <p class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</p>
                        @if($product->compare_at_price && $product->compare_at_price > $product->price)
                            <p class="text-sm text-gray-500 line-through">${{ number_format($product->compare_at_price, 2) }}</p>
                        @endif
                    @endif
                </div>
                
                @if($product->average_rating)
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="ml-1 text-sm text-gray-600">{{ number_format($product->average_rating, 1) }}</span>
                    </div>
                @endif
            </div>
            
            @if($product->stock_quantity !== null)
                @if($product->stock_quantity > 0)
                    <p class="mt-2 text-sm text-green-600">In Stock ({{ $product->stock_quantity }})</p>
                @else
                    <p class="mt-2 text-sm text-red-600">Out of Stock</p>
                @endif
            @endif
        </div>
    </a>
</div>