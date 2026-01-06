<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value' => 0,
    'label' => '',
    'icon' => 'fas fa-chart-bar',
    'color' => 'blue', // blue, yellow, green, indigo, purple, teal, orange, red
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
    'value' => 0,
    'label' => '',
    'icon' => 'fas fa-chart-bar',
    'color' => 'blue', // blue, yellow, green, indigo, purple, teal, orange, red
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
?>

<div class="flex-1 min-w-0 bg-white rounded-lg shadow-sm p-4 border-l-4 <?php echo e($c['border']); ?>">
    <div class="flex items-center">
        <div class="w-10 h-10 <?php echo e($c['bg']); ?> rounded-full flex items-center justify-center mr-3 flex-shrink-0">
            <i class="<?php echo e($icon); ?> <?php echo e($c['text']); ?>"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?php echo e($value); ?></p>
            <p class="text-xs text-gray-500"><?php echo e($label); ?></p>
        </div>
    </div>
</div>
<?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/components/stat-card.blade.php ENDPATH**/ ?>