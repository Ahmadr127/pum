{{--
 modal-scanpdf.blade.php
 Opened via Alpine event 'open-scan-pdf'.
 Emits 'scan-result' with parsed data + raw File object.
 JS logic lives in /js/pum/scanpdf.js
--}}
<div x-data="pdfScanner()"
     x-show="open"
     x-on:open-scan-pdf.window="open = true; reset()"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.55);">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
         @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-indigo-500">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-file-pdf text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Scan PDF Pengajuan</h3>
            </div>
            <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">

            {{-- IDLE: Drop Zone --}}
            <div x-show="state === 'idle'" class="space-y-4">
                <p class="text-sm text-gray-500 text-center">
                    Upload file PDF "Detail Pengajuan" untuk membaca datanya secara otomatis.
                </p>

                <label for="pdfFileInput"
                       class="flex flex-col items-center justify-center gap-3 w-full h-40 border-2 border-dashed border-indigo-300 rounded-xl cursor-pointer bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-500 transition-all"
                       @dragover.prevent
                       @drop.prevent="handleDrop($event)">
                    <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-cloud-upload-alt text-3xl text-indigo-400"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-indigo-600">Klik atau seret PDF ke sini</p>
                        <p class="text-xs text-gray-400 mt-1">Format: PDF, maks. 10 MB</p>
                    </div>
                    <input id="pdfFileInput" type="file" accept="application/pdf" class="hidden"
                           @change="handleFileChange($event)">
                </label>

                <div x-show="errorMsg" class="flex items-center gap-2 text-red-600 text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span x-text="errorMsg"></span>
                </div>
            </div>

            {{-- LOADING: Spinner --}}
            <div x-show="state === 'loading'" class="flex flex-col items-center justify-center py-10 gap-5">
                <div class="relative">
                    <div class="w-20 h-20 rounded-full border-4 border-indigo-100"></div>
                    <div class="absolute inset-0 w-20 h-20 rounded-full border-4 border-indigo-500 border-t-transparent animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-file-pdf text-indigo-400 text-2xl"></i>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-base font-semibold text-gray-800">Membaca PDF...</p>
                    <p class="text-sm text-gray-400 mt-1">Mohon tunggu sebentar</p>
                </div>
            </div>

        </div>
    </div>
</div>

@once
@push('scripts')
{{-- PDF.js CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
{{-- Scan + parser logic --}}
<script src="{{ asset('js/pum/scanpdf.js') }}"></script>
@endpush
@endonce
