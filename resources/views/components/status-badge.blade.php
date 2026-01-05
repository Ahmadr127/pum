@props([
    'status' => 'new',
    'size' => 'md'
])

@php
    $colors = [
        'new' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'pending' => 'bg-blue-100 text-blue-800 border-blue-200',
        'approved' => 'bg-green-100 text-green-800 border-green-200',
        'rejected' => 'bg-red-100 text-red-800 border-red-200',
        'fulfilled' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    ];

    $labels = [
        'new' => 'Baru',
        'pending' => 'Menunggu Persetujuan',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'fulfilled' => 'Terpenuhi',
    ];

    $icons = [
        'new' => 'fas fa-file-alt',
        'pending' => 'fas fa-clock',
        'approved' => 'fas fa-check-circle',
        'rejected' => 'fas fa-times-circle',
        'fulfilled' => 'fas fa-check-double',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1.5 text-base',
    ];

    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    $label = $labels[$status] ?? ucfirst($status);
    $icon = $icons[$status] ?? 'fas fa-question-circle';
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full border $colorClass $sizeClass"]) }}>
    <i class="{{ $icon }} mr-1.5 text-xs"></i>
    {{ $label }}
</span>
