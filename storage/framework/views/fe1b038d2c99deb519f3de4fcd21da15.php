<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'status' => 'new',
    'size' => 'md'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'status' => 'new',
    'size' => 'md'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<span <?php echo e($attributes->merge(['class' => "inline-flex items-center font-medium rounded-full border $colorClass $sizeClass"])); ?>>
    <i class="<?php echo e($icon); ?> mr-1.5 text-xs"></i>
    <?php echo e($label); ?>

</span>
<?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/components/status-badge.blade.php ENDPATH**/ ?>