@props([
    'name' => 'amount',
    'value' => '',
    'label' => 'Jumlah',
    'required' => false,
    'placeholder' => '0',
    'readonly' => false,
])

<div x-data="{
    rawValue: '{{ $value }}',
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
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    
    <div class="relative">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-medium">
            Rp
        </span>
        
        <!-- Hidden input for actual value -->
        <input 
            type="hidden" 
            name="{{ $name }}" 
            x-model="rawValue"
        >
        
        <!-- Display input -->
        <input 
            type="text" 
            id="{{ $name }}"
            x-model="formattedValue"
            @input="parseValue($event.target.value)"
            @blur="formatValue()"
            placeholder="{{ $placeholder }}"
            inputmode="numeric"
            @if($readonly) readonly @endif
            {{ $attributes->merge(['class' => 'block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-right' . ($readonly ? ' bg-gray-100' : '')]) }}
        >
    </div>
</div>
