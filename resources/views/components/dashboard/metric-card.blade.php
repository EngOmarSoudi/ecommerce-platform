@props(['title', 'value', 'icon', 'trend' => null, 'color' => 'primary'])

<div class="card hover:shadow-lg transition-shadow duration-200">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
            @if($trend)
                <p class="mt-2 text-sm {{ $trend > 0 ? 'text-green-600' : 'text-red-600' }}">
                    @if($trend > 0)
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    @else
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    @endif
                    {{ abs($trend) }}% from last month
                </p>
            @endif
        </div>
        <div class="p-3 rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900/20">
            {!! $icon !!}
        </div>
    </div>
</div>
