@props([
    'name' => 'modal',
    'maxWidth' => 'lg',
    'title' => '',
    'icon' => null,
    'iconColor' => 'blue'
])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth] ?? 'sm:max-w-lg';

$iconColorClass = [
    'green' => 'bg-green-100 text-green-600',
    'red' => 'bg-red-100 text-red-600',
    'blue' => 'bg-blue-100 text-blue-600',
    'yellow' => 'bg-yellow-100 text-yellow-600',
    'orange' => 'bg-orange-100 text-orange-600',
][$iconColor] ?? 'bg-blue-100 text-blue-600';
@endphp

<div 
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="open = false"
    ></div>

    {{-- Modal Container --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            {{-- Modal Panel --}}
            <div 
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full {{ $maxWidthClass }}"
                @click.away="open = false"
            >
                {{-- Header with Icon --}}
                @if($title || $icon)
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        @if($icon)
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full {{ $iconColorClass }} sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        @endif
                        <div class="mt-3 text-center sm:mt-0 {{ $icon ? 'sm:ml-4' : '' }} sm:text-left flex-1">
                            @if($title)
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                {{ $title }}
                            </h3>
                            @endif
                            <div class="mt-2">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    {{ $slot }}
                </div>
                @endif

                {{-- Footer --}}
                @if(isset($footer))
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    {{ $footer }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
