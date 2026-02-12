<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value' => 0,
    'label' => '',
    'icon' => 'fas fa-chart-bar',
    'color' => 'blue', // blue, yellow, green, indigo, purple, teal, orange, red
    'subValue' => null,
    'statusMessage' => null,
    'statusType' => 'success', // success, warning, danger
    'href' => null,
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
    'subValue' => null,
    'statusMessage' => null,
    'statusType' => 'success', // success, warning, danger
    'href' => null,
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
?>

<<?php echo e($tag); ?> <?php if($href): ?> href="<?php echo e($href); ?>" <?php endif; ?> class="flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 p-5 <?php echo e($hoverClass); ?>">
    
    <div class="flex justify-between items-start mb-4">
        
        <div class="w-12 h-12 rounded-xl <?php echo e($c['bg']); ?> flex items-center justify-center shadow-sm">
            <i class="<?php echo e($icon); ?> text-white text-xl"></i>
        </div>
        
        
        <div class="text-right">
            <h3 class="text-2xl font-bold text-gray-900 leading-none"><?php echo e($value); ?></h3>
            <span class="text-xs text-gray-500 font-medium mt-1 block">Total</span>
        </div>
    </div>

    
    <div class="mb-auto">
        <h4 class="font-semibold text-gray-800 text-md mb-2"><?php echo e($label); ?></h4>
        
        <?php if($statusMessage): ?>
            <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo e($statusClass); ?>">
                <i class="<?php echo e($statusIcon); ?> mr-1.5"></i>
                <?php echo e($statusMessage); ?>

            </div>
        <?php endif; ?>
    </div>

    
    <?php if($subValue !== null || $href): ?>
        <div class="mt-4 pt-3 border-t border-gray-50 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <?php if($subValue !== null): ?>
                    Bulan ini: <span class="font-medium text-gray-700"><?php echo e($subValue); ?></span>
                <?php else: ?>
                   &nbsp;
                <?php endif; ?>
            </div>
            
            <?php if($href): ?>
                <div class="w-6 h-6 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                    <i class="fas fa-arrow-right text-xs"></i>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</<?php echo e($tag); ?>>
<?php /**PATH D:\Pemrograman\magang\pum\resources\views/components/stat-card.blade.php ENDPATH**/ ?>