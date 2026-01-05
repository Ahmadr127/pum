@props([
    'value' => 0,
    'label' => '',
    'icon' => 'fas fa-chart-bar',
    'color' => 'blue', // blue, yellow, green, indigo, purple, teal, orange, red
])

@php
    $colorClasses = [
        'blue' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
        'yellow' => ['border' => 'border-yellow-500', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
        'green' => ['border' => 'border-green-500', 'bg' => 'bg-green-100', 'text' => 'text-green-600'],
        'indigo' => ['border' => 'border-indigo-500', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        'purple' => ['border' => 'border-purple-500', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
        'teal' => ['border' => 'border-teal-500', 'bg' => 'bg-teal-100', 'text' => 'text-teal-600'],
        'orange' => ['border' => 'border-orange-500', 'bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
        'red' => ['border' => 'border-red-500', 'bg' => 'bg-red-100', 'text' => 'text-red-600'],
    ];
    $c = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="flex-1 min-w-0 bg-white rounded-lg shadow-sm p-4 border-l-4 {{ $c['border'] }}">
    <div class="flex items-center">
        <div class="w-10 h-10 {{ $c['bg'] }} rounded-full flex items-center justify-center mr-3 flex-shrink-0">
            <i class="{{ $icon }} {{ $c['text'] }}"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
            <p class="text-xs text-gray-500">{{ $label }}</p>
        </div>
    </div>
</div>
