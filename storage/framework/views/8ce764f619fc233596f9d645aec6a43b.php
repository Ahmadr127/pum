

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => null,
    'options' => [],
    'valueField' => 'id',
    'labelField' => 'name',
    'groupField' => null,
    'selected' => null,
    'placeholder' => 'Pilih...',
    'required' => false,
    'disabled' => false,
    'emptyOption' => null,
    'error' => null
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
    'name',
    'label' => null,
    'options' => [],
    'valueField' => 'id',
    'labelField' => 'name',
    'groupField' => null,
    'selected' => null,
    'placeholder' => 'Pilih...',
    'required' => false,
    'disabled' => false,
    'emptyOption' => null,
    'error' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $inputId = 'dropdown-' . Str::random(8);
    $selectedValue = old($name, $selected);
?>

<div 
    x-data="searchableDropdown({
        options: <?php echo e(Js::from($options->map(fn($opt) => [
            'value' => data_get($opt, $valueField),
            'label' => data_get($opt, $labelField),
            'group' => $groupField ? data_get($opt, $groupField) : null,
            'raw' => $opt
        ]))); ?>,
        selected: <?php echo e(Js::from($selectedValue)); ?>,
        placeholder: '<?php echo e($placeholder); ?>',
        emptyOption: <?php echo e(Js::from($emptyOption)); ?>

    })"
    class="relative"
    @click.away="close()"
    style="z-index: 10;"
>
    <?php if($label): ?>
    <label for="<?php echo e($inputId); ?>" class="block text-sm font-medium text-gray-700 mb-1">
        <?php echo e($label); ?>

        <?php if($required): ?>
            <span class="text-red-500">*</span>
        <?php endif; ?>
    </label>
    <?php endif; ?>
    
    
    <input type="hidden" name="<?php echo e($name); ?>" x-model="selectedValue">
    
    
    <button
        type="button"
        id="<?php echo e($inputId); ?>"
        @click="toggle()"
        :disabled="<?php echo e($disabled ? 'true' : 'false'); ?>"
        class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm <?php echo e($disabled ? 'bg-gray-100 cursor-not-allowed' : ''); ?>"
        :class="{ 'ring-1 ring-green-500 border-green-500': open }"
    >
        <span x-text="displayText" class="block truncate" :class="{ 'text-gray-400': !selectedValue }"></span>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
        </span>
    </button>
    
    
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-ref="dropdown"
        class="fixed bg-white shadow-xl max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        :style="dropdownStyle"
        style="z-index: 99999;"
        x-cloak
    >
        
        <div class="sticky top-0 z-10 bg-white px-2 py-2 border-b border-gray-100">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input
                    type="text"
                    x-model="search"
                    x-ref="searchInput"
                    @keydown.escape="close()"
                    @keydown.enter.prevent="selectFirst()"
                    placeholder="Cari..."
                    class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500"
                >
            </div>
        </div>
        
        
        <ul class="py-1">
            
            <template x-if="emptyOption !== null">
                <li
                    @click="select(null)"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-green-50"
                    :class="{ 'bg-green-50 text-green-900': selectedValue === null }"
                >
                    <span class="block truncate text-gray-500" x-text="emptyOption || '-- Tidak Ada --'"></span>
                    <span x-show="selectedValue === null" class="absolute inset-y-0 right-0 flex items-center pr-4 text-green-600">
                        <i class="fas fa-check text-xs"></i>
                    </span>
                </li>
            </template>
            
            
            <template x-for="(option, index) in filteredOptions" :key="option.value">
                <li
                    @click="select(option.value)"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-green-50"
                    :class="{ 'bg-green-100 text-green-900': selectedValue == option.value }"
                >
                    <span class="block truncate" x-text="option.label"></span>
                    <span x-show="option.group" class="text-xs text-gray-400 ml-1" x-text="'(' + option.group + ')'"></span>
                    <span x-show="selectedValue == option.value" class="absolute inset-y-0 right-0 flex items-center pr-4 text-green-600">
                        <i class="fas fa-check text-xs"></i>
                    </span>
                </li>
            </template>
            
            
            <template x-if="filteredOptions.length === 0 && search">
                <li class="py-2 pl-3 pr-9 text-gray-500 text-sm">
                    Tidak ada hasil untuk "<span x-text="search"></span>"
                </li>
            </template>
        </ul>
    </div>
    
    
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<?php if (! $__env->hasRenderedOnce('60545226-36e9-4fdf-8ccb-348520eb6f0c')): $__env->markAsRenderedOnce('60545226-36e9-4fdf-8ccb-348520eb6f0c'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
function searchableDropdown(config) {
    return {
        open: false,
        search: '',
        dropUp: false,
        dropdownPosition: { top: 0, left: 0, width: 0 },
        options: config.options || [],
        selectedValue: config.selected,
        placeholder: config.placeholder || 'Pilih...',
        emptyOption: config.emptyOption,
        
        get filteredOptions() {
            if (!this.search) return this.options;
            const query = this.search.toLowerCase();
            return this.options.filter(opt => 
                (opt.label && opt.label.toLowerCase().includes(query)) ||
                (opt.group && opt.group.toLowerCase().includes(query))
            );
        },
        
        get displayText() {
            if (this.selectedValue === null || this.selectedValue === '') {
                return this.placeholder;
            }
            const found = this.options.find(opt => opt.value == this.selectedValue);
            return found ? found.label : this.placeholder;
        },
        
        get dropdownStyle() {
            if (this.dropUp) {
                return `bottom: ${window.innerHeight - this.dropdownPosition.top + 4}px; left: ${this.dropdownPosition.left}px; width: ${this.dropdownPosition.width}px;`;
            }
            return `top: ${this.dropdownPosition.top + this.dropdownPosition.height + 4}px; left: ${this.dropdownPosition.left}px; width: ${this.dropdownPosition.width}px;`;
        },
        
        updatePosition() {
            const rect = this.$el.getBoundingClientRect();
            this.dropdownPosition = {
                top: rect.top,
                left: rect.left,
                width: rect.width,
                height: rect.height
            };
            
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            this.dropUp = spaceBelow < 250 && spaceAbove > spaceBelow;
        },
        
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.updatePosition();
                this.$nextTick(() => {
                    this.$refs.searchInput?.focus();
                });
            }
        },
        
        close() {
            this.open = false;
            this.search = '';
        },
        
        select(value) {
            this.selectedValue = value;
            this.close();
        },
        
        selectFirst() {
            if (this.filteredOptions.length > 0) {
                this.select(this.filteredOptions[0].value);
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH D:\Pemrograman\magang\pum\resources\views/components/searchable-dropdown.blade.php ENDPATH**/ ?>