@props([
    'type' => 'default', // default, success, warning, error, info, primary
    'size' => 'md', // sm, md, lg
    'rounded' => true
])

@php
$typeClasses = [
    'default' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    'success' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'error' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'primary' => 'bg-brand.primary/10 text-brand.primary dark:bg-brand.primary/20',
];

$sizeClasses = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-xs',
    'lg' => 'px-4 py-1.5 text-sm',
];

$roundedClass = $rounded ? 'rounded-full' : 'rounded';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center font-medium ' . $typeClasses[$type] . ' ' . $sizeClasses[$size] . ' ' . $roundedClass]) }}>
    {{ $slot }}
</span>
