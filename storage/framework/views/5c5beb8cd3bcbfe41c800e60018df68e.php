<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'amount',
    'value' => '',
    'label' => 'Jumlah',
    'required' => false,
    'placeholder' => '0',
    'readonly' => false,
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
    'name' => 'amount',
    'value' => '',
    'label' => 'Jumlah',
    'required' => false,
    'placeholder' => '0',
    'readonly' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{
    rawValue: '<?php echo e($value); ?>',
    formattedValue: '',
    
    init() {
        this.formatValue();
    },
    
    formatValue() {
        if (this.rawValue === '' || this.rawValue === null) {
            this.formattedValue = '';
            return;
        }
        
        const num = parseFloat(this.rawValue);
        if (!isNaN(num)) {
            this.formattedValue = new Intl.NumberFormat('id-ID').format(num);
        }
    },
    
    parseValue(formatted) {
        // Remove thousand separators and convert comma to dot for decimals
        const cleaned = formatted.replace(/\./g, '').replace(/,/g, '.');
        const num = parseFloat(cleaned);
        
        if (isNaN(num)) {
            this.rawValue = '';
        } else {
            this.rawValue = num.toString();
        }
        
        this.formatValue();
    }
}">
    <?php if($label): ?>
    <label for="<?php echo e($name); ?>" class="block text-sm font-medium text-gray-700 mb-1">
        <?php echo e($label); ?>

        <?php if($required): ?>
        <span class="text-red-500">*</span>
        <?php endif; ?>
    </label>
    <?php endif; ?>
    
    <div class="relative">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-medium">
            Rp
        </span>
        
        <!-- Hidden input for actual value -->
        <input 
            type="hidden" 
            name="<?php echo e($name); ?>" 
            x-model="rawValue"
        >
        
        <!-- Display input -->
        <input 
            type="text" 
            id="<?php echo e($name); ?>"
            x-model="formattedValue"
            @input="parseValue($event.target.value)"
            @blur="formatValue()"
            placeholder="<?php echo e($placeholder); ?>"
            inputmode="numeric"
            <?php if($readonly): ?> readonly <?php endif; ?>
            <?php echo e($attributes->merge(['class' => 'block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-right' . ($readonly ? ' bg-gray-100' : '')])); ?>

        >
    </div>
</div>
<?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/components/currency-input.blade.php ENDPATH**/ ?>