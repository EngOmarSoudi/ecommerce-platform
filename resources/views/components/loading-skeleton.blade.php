@props([
    'type' => 'card', // card, table, list, product, stats
    'count' => 1,
    'class' => ''
])

<div {{ $attributes->merge(['class' => $class]) }}>
    @if($type === 'card')
        @for($i = 0; $i < $count; $i++)
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-4"></div>
            <div class="space-y-3">
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-4/6"></div>
            </div>
        </div>
        @endfor
    
    @elseif($type === 'table')
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="h-12 bg-gray-100 dark:bg-gray-700"></div>
            @for($i = 0; $i < $count; $i++)
            <div class="border-t dark:border-gray-700 p-4 space-y-3">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
            </div>
            @endfor
        </div>
    
    @elseif($type === 'product')
        @for($i = 0; $i < $count; $i++)
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="aspect-square bg-gray-200 dark:bg-gray-700"></div>
            <div class="p-4 space-y-3">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
            </div>
        </div>
        @endfor
    
    @elseif($type === 'list')
        @for($i = 0; $i < $count; $i++)
        <div class="animate-pulse flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg mb-3">
            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
            </div>
        </div>
        @endfor
    
    @elseif($type === 'stats')
        @for($i = 0; $i < $count; $i++)
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
        </div>
        @endfor
    @endif
</div>
