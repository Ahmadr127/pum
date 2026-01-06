<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'href' => '#',
    'icon' => 'fas fa-link',
    'title' => '',
    'subtitle' => '',
    'color' => 'blue', // green, orange, blue, purple, teal, indigo
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
    'href' => '#',
    'icon' => 'fas fa-link',
    'title' => '',
    'subtitle' => '',
    'color' => 'blue', // green, orange, blue, purple, teal, indigo
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $colorClasses = [
        'green' => ['bg' => 'bg-green-50', 'hover' => 'hover:bg-green-100', 'icon' => 'bg-green-600 group-hover:bg-green-700', 'title' => 'text-green-900', 'subtitle' => 'text-green-600'],
        'orange' => ['bg' => 'bg-orange-50', 'hover' => 'hover:bg-orange-100', 'icon' => 'bg-orange-600 group-hover:bg-orange-700', 'title' => 'text-orange-900', 'subtitle' => 'text-orange-600'],
        'blue' => ['bg' => 'bg-blue-50', 'hover' => 'hover:bg-blue-100', 'icon' => 'bg-blue-600 group-hover:bg-blue-700', 'title' => 'text-blue-900', 'subtitle' => 'text-blue-600'],
        'purple' => ['bg' => 'bg-purple-50', 'hover' => 'hover:bg-purple-100', 'icon' => 'bg-purple-600 group-hover:bg-purple-700', 'title' => 'text-purple-900', 'subtitle' => 'text-purple-600'],
        'teal' => ['bg' => 'bg-teal-50', 'hover' => 'hover:bg-teal-100', 'icon' => 'bg-teal-600 group-hover:bg-teal-700', 'title' => 'text-teal-900', 'subtitle' => 'text-teal-600'],
        'indigo' => ['bg' => 'bg-indigo-50', 'hover' => 'hover:bg-indigo-100', 'icon' => 'bg-indigo-600 group-hover:bg-indigo-700', 'title' => 'text-indigo-900', 'subtitle' => 'text-indigo-600'],
    ];
    $c = $colorClasses[$color] ?? $colorClasses['blue'];
?>

<a href="<?php echo e($href); ?>" class="flex items-center p-4 <?php echo e($c['bg']); ?> rounded-lg <?php echo e($c['hover']); ?> transition-colors group">
    <div class="w-10 h-10 <?php echo e($c['icon']); ?> rounded-lg flex items-center justify-center mr-4 transition-colors">
        <i class="<?php echo e($icon); ?> text-white"></i>
    </div>
    <div>
        <div class="<?php echo e($c['title']); ?> font-medium"><?php echo e($title); ?></div>
        <div class="text-sm <?php echo e($c['subtitle']); ?>"><?php echo e($subtitle); ?></div>
    </div>
</a>
<?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/components/quick-action.blade.php ENDPATH**/ ?>