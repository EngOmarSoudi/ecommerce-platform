@props([
    'size' => 'md', // xs, sm, md, lg, xl
    'color' => 'primary', // primary, white, gray
    'class' => ''
])

@php
$sizeClasses = [
    'xs' => 'w-4 h-4',
    'sm' => 'w-6 h-6',
    'md' => 'w-8 h-8',
    'lg' => 'w-12 h-12',
    'xl' => 'w-16 h-16',
];

$colorClasses = [
    'primary' => 'border-brand.primary',
    'white' => 'border-white',
    'gray' => 'border-gray-600',
];
@endphp

<div {{ $attributes->merge(['class' => $class]) }}>
    <div class="inline-block {{ $sizeClasses[$size] }} {{ $colorClasses[$color] }} border-4 border-t-transparent rounded-full animate-spin"></div>
</div>
