@props([
    'value' => 0,
    'label' => '',
    'icon' => 'fas fa-chart-bar',
    'color' => 'blue', // blue, yellow, green, indigo, purple, teal, orange, red
    'subValue' => null,
    'statusMessage' => null,
    'statusType' => 'success', // success, warning, danger
    'href' => null,
])

@php
    $colorClasses = [
        'blue' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600', 'light' => 'bg-blue-50'],
        'yellow' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-600', 'light' => 'bg-yellow-50'],
        'green' => ['bg' => 'bg-green-500', 'text' => 'text-green-600', 'light' => 'bg-green-50'],
        'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'light' => 'bg-indigo-50'],
        'purple' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-600', 'light' => 'bg-purple-50'],
        'teal' => ['bg' => 'bg-teal-500', 'text' => 'text-teal-600', 'light' => 'bg-teal-50'],
        'orange' => ['bg' => 'bg-orange-500', 'text' => 'text-orange-600', 'light' => 'bg-orange-50'],
        'red' => ['bg' => 'bg-red-500', 'text' => 'text-red-600', 'light' => 'bg-red-50'],
        'gray' => ['bg' => 'bg-gray-500', 'text' => 'text-gray-600', 'light' => 'bg-gray-50'],
    ];
    
    // Default to blue if color not found
    $c = $colorClasses[$color] ?? $colorClasses['blue'];

    $statusColors = [
        'success' => 'bg-green-100 text-green-700',
        'warning' => 'bg-yellow-100 text-yellow-700',
        'danger' => 'bg-red-100 text-red-700',
    ];
    $statusClass = $statusColors[$statusType] ?? $statusColors['success'];
    $statusIcon = match($statusType) {
        'success' => 'fas fa-check-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'danger' => 'fas fa-times-circle',
        default => 'fas fa-info-circle'
    };
    
    // Wrapper tag logic
    $tag = $href ? 'a' : 'div';
    $hoverClass = $href ? 'hover:shadow-md transition-shadow duration-200 cursor-pointer' : '';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif class="flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 p-5 {{ $hoverClass }}">
    {{-- Top Section: Icon & Value --}}
    <div class="flex justify-between items-start mb-4">
        {{-- Icon Box --}}
        <div class="w-12 h-12 rounded-xl {{ $c['bg'] }} flex items-center justify-center shadow-sm">
            <i class="{{ $icon }} text-white text-xl"></i>
        </div>
        
        {{-- Value --}}
        <div class="text-right">
            <h3 class="text-2xl font-bold text-gray-900 leading-none">{{ $value }}</h3>
            <span class="text-xs text-gray-500 font-medium mt-1 block">Total</span>
        </div>
    </div>

    {{-- Middle Section: Label & Status --}}
    <div class="mb-auto">
        <h4 class="font-semibold text-gray-800 text-md mb-2">{{ $label }}</h4>
        
        @if($statusMessage)
            <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                <i class="{{ $statusIcon }} mr-1.5"></i>
                {{ $statusMessage }}
            </div>
        @endif
    </div>

    {{-- Footer Section: SubValue & Arrow --}}
    @if($subValue !== null || $href)
        <div class="mt-4 pt-3 border-t border-gray-50 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                @if($subValue !== null)
                    Bulan ini: <span class="font-medium text-gray-700">{{ $subValue }}</span>
                @else
                   &nbsp;
                @endif
            </div>
            
            @if($href)
                <div class="w-6 h-6 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                    <i class="fas fa-arrow-right text-xs"></i>
                </div>
            @endif
        </div>
    @endif
</{{ $tag }}>
