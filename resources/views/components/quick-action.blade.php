@props([
    'href' => '#',
    'icon' => 'fas fa-link',
    'title' => '',
    'subtitle' => '',
    'color' => 'blue', // green, orange, blue, purple, teal, indigo
])

@php
    $colorClasses = [
        'green' => ['bg' => 'bg-green-50', 'hover' => 'hover:bg-green-100', 'icon' => 'bg-green-600 group-hover:bg-green-700', 'title' => 'text-green-900', 'subtitle' => 'text-green-600'],
        'orange' => ['bg' => 'bg-orange-50', 'hover' => 'hover:bg-orange-100', 'icon' => 'bg-orange-600 group-hover:bg-orange-700', 'title' => 'text-orange-900', 'subtitle' => 'text-orange-600'],
        'blue' => ['bg' => 'bg-blue-50', 'hover' => 'hover:bg-blue-100', 'icon' => 'bg-blue-600 group-hover:bg-blue-700', 'title' => 'text-blue-900', 'subtitle' => 'text-blue-600'],
        'purple' => ['bg' => 'bg-purple-50', 'hover' => 'hover:bg-purple-100', 'icon' => 'bg-purple-600 group-hover:bg-purple-700', 'title' => 'text-purple-900', 'subtitle' => 'text-purple-600'],
        'teal' => ['bg' => 'bg-teal-50', 'hover' => 'hover:bg-teal-100', 'icon' => 'bg-teal-600 group-hover:bg-teal-700', 'title' => 'text-teal-900', 'subtitle' => 'text-teal-600'],
        'indigo' => ['bg' => 'bg-indigo-50', 'hover' => 'hover:bg-indigo-100', 'icon' => 'bg-indigo-600 group-hover:bg-indigo-700', 'title' => 'text-indigo-900', 'subtitle' => 'text-indigo-600'],
    ];
    $c = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<a href="{{ $href }}" class="flex items-center p-4 {{ $c['bg'] }} rounded-lg {{ $c['hover'] }} transition-colors group">
    <div class="w-10 h-10 {{ $c['icon'] }} rounded-lg flex items-center justify-center mr-4 transition-colors">
        <i class="{{ $icon }} text-white"></i>
    </div>
    <div>
        <div class="{{ $c['title'] }} font-medium">{{ $title }}</div>
        <div class="text-sm {{ $c['subtitle'] }}">{{ $subtitle }}</div>
    </div>
</a>
