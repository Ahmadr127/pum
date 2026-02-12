@extends('layouts.app')

@section('title', 'Edit Permintaan - ' . $pumRequest->code)

@section('content')
<div class="w-full py-8">
    <div class="max-w-full mx-auto px-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Pengajuan</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-sm text-gray-500">Kode:</span>
                    <span class="font-mono font-medium text-gray-700 bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $pumRequest->code }}</span>
                    <x-status-badge :status="$pumRequest->status" />
                </div>
            </div>
            <a href="{{ route('pum-requests.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('pum-requests.update', $pumRequest) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-8 space-y-8">
                    
                    <!-- Section 1: Informasi Dasar -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pengaju</label>
                            <input type="hidden" name="requester_id" value="{{ $pumRequest->requester_id }}">
                            <div class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold mr-3 text-sm">
                                    {{ substr($pumRequest->requester->name ?? 'User', 0, 2) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $pumRequest->requester->name ?? '-' }}</span>
                            </div>
                        </div>

                        <div>
                            <label for="request_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengajuan <span class="text-red-500">*</span></label>
                            <input type="date" name="request_date" id="request_date" 
                                   value="{{ old('request_date', $pumRequest->request_date->format('Y-m-d')) }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            @error('request_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <!-- Section 2: Kategori & Nominal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Pengadaan <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors {{ old('procurement_category', $pumRequest->procurement_category) === 'barang_baru' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                    <input type="radio" name="procurement_category" value="barang_baru" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" {{ old('procurement_category', $pumRequest->procurement_category) === 'barang_baru' ? 'checked' : '' }} required>
                                    <span class="ml-3 flex items-center">
                                        <i class="fas fa-box text-lg mr-3 {{ old('procurement_category', $pumRequest->procurement_category) === 'barang_baru' ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                                        <span class="text-sm font-medium text-gray-900">Barang Baru</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors {{ old('procurement_category', $pumRequest->procurement_category) === 'peremajaan' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                    <input type="radio" name="procurement_category" value="peremajaan" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" {{ old('procurement_category', $pumRequest->procurement_category) === 'peremajaan' ? 'checked' : '' }} required>
                                    <span class="ml-3 flex items-center">
                                        <i class="fas fa-sync-alt text-lg mr-3 {{ old('procurement_category', $pumRequest->procurement_category) === 'peremajaan' ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                                        <span class="text-sm font-medium text-gray-900">Peremajaan</span>
                                    </span>
                                </label>
                            </div>
                            @error('procurement_category')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Total Nominal (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <x-currency-input 
                                    name="amount" 
                                    :value="old('amount', $pumRequest->amount)" 
                                    label=""
                                    placeholder="0"
                                    required
                                    class="text-xl font-semibold text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg py-3 px-4 w-full"
                                />
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 3: Keterangan -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Keterangan / Keperluan</label>
                        <textarea name="description" id="description" rows="4"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                                  placeholder="Jelaskan secara rinci tujuan permintaan uang muka ini...">{{ old('description', $pumRequest->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <!-- Section 4: Lampiran -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-4">Dokumen Pendukung</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Lampiran 1 -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Lampiran Utama (Wajib)</label>
                                
                                <!-- Existing Attachments -->
                                @if($pumRequest->attachments && count($pumRequest->attachments) > 0)
                                    <div class="mb-4 space-y-2">
                                        @foreach($pumRequest->attachments as $index => $attachment)
                                        <div class="flex items-center justify-between p-3 bg-indigo-50 border border-indigo-100 rounded-lg text-sm">
                                            <div class="flex items-center gap-3 overflow-hidden">
                                                <i class="fas fa-file text-indigo-500"></i>
                                                <a href="{{ Storage::url($attachment) }}" target="_blank" class="truncate text-indigo-700 hover:text-indigo-900 font-medium">
                                                    {{ basename($attachment) }}
                                                </a>
                                            </div>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="remove_attachments[]" value="{{ $index }}" class="rounded text-red-600 border-gray-300 focus:ring-red-500 w-4 h-4">
                                                <span class="text-xs text-red-600 font-medium">Hapus</span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div x-data="fileUpload('attachments')" class="w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer hover:bg-gray-50 transition-all"
                                            :class="{ 'border-indigo-500 bg-indigo-50': dragover }"
                                            @dragover.prevent="dragover = true"
                                            @dragleave.prevent="dragover = false"
                                            @drop.prevent="handleDrop($event)">
                                        
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="files.length === 0">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-500">Upload Dokumen/Invoice</p>
                                        </div>

                                        <div class="w-full px-4" x-show="files.length > 0">
                                            <template x-for="(file, index) in files" :key="index">
                                                <div class="flex items-center justify-between p-2 mb-1 bg-white rounded border border-gray-200 text-xs">
                                                    <span class="truncate text-gray-600" x-text="file.name"></span>
                                                    <button type="button" @click="removeFile(index)" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                                                </div>
                                            </template>
                                        </div>
                                        <input type="file" name="attachments[]" multiple x-ref="fileInput" class="hidden" @change="handleFiles($event)" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    </label>
                                </div>
                                @error('attachments')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lampiran 2 -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Lampiran Tambahan(Opsional)</label>
                                
                                <!-- Existing Attachments 2 -->
                                @if($pumRequest->attachments2 && count($pumRequest->attachments2) > 0)
                                    <div class="mb-4 space-y-2">
                                        @foreach($pumRequest->attachments2 as $index => $attachment)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                                            <div class="flex items-center gap-3 overflow-hidden">
                                                <i class="fas fa-paperclip text-gray-500"></i>
                                                <a href="{{ Storage::url($attachment) }}" target="_blank" class="truncate text-gray-700 hover:text-gray-900 font-medium">
                                                    {{ basename($attachment) }}
                                                </a>
                                            </div>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="remove_attachments2[]" value="{{ $index }}" class="rounded text-red-600 border-gray-300 focus:ring-red-500 w-4 h-4">
                                                <span class="text-xs text-red-600 font-medium">Hapus</span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div x-data="fileUpload('attachments2')" class="w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer hover:bg-gray-50 transition-all"
                                            :class="{ 'border-indigo-500 bg-indigo-50': dragover }"
                                            @dragover.prevent="dragover = true"
                                            @dragleave.prevent="dragover = false"
                                            @drop.prevent="handleDrop($event)">
                                        
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="files.length === 0">
                                            <i class="fas fa-paperclip text-2xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-500">Upload File Pendukung</p>
                                        </div>

                                        <div class="w-full px-4" x-show="files.length > 0">
                                            <template x-for="(file, index) in files" :key="index">
                                                <div class="flex items-center justify-between p-2 mb-1 bg-white rounded border border-gray-200 text-xs">
                                                    <span class="truncate text-gray-600" x-text="file.name"></span>
                                                    <button type="button" @click="removeFile(index)" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                                                </div>
                                            </template>
                                        </div>
                                        <input type="file" name="attachments2[]" multiple x-ref="fileInput" class="hidden" @change="handleFiles($event)" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 px-8 py-5 flex items-center justify-end gap-3 border-t border-gray-200">
                    <a href="{{ route('pum-requests.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm">
                        Batal
                    </a>
                    <button type="submit" name="submit_for_approval" value="0" 
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm">
                        Simpan Draft
                    </button>
                    <button type="submit" name="submit_for_approval" value="1" 
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors text-sm shadow-sm flex items-center gap-2">
                        <span>Simpan Perubahan</span>
                        <i class="fas fa-save text-xs"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function fileUpload(inputName = 'attachments') {
    return {
        files: [],
        dragover: false,
        inputName: inputName,
        
        handleFiles(event) {
            const newFiles = Array.from(event.target.files);
            this.addFiles(newFiles);
        },
        
        handleDrop(event) {
            this.dragover = false;
            const newFiles = Array.from(event.dataTransfer.files);
            this.addFiles(newFiles);
        },
        
        addFiles(newFiles) {
            newFiles.forEach(file => {
                if (file.size <= 5 * 1024 * 1024) {
                    this.files.push(file);
                } else {
                    alert(`File ${file.name} terlalu besar (max 5MB)`);
                }
            });
            this.updateInput();
        },
        
        removeFile(index) {
            this.files.splice(index, 1);
            this.updateInput();
        },
        
        updateInput() {
            const dt = new DataTransfer();
            this.files.forEach(file => dt.items.add(file));
            this.$refs.fileInput.files = dt.files;
        },
        
        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }
    }
}
</script>
@endpush
@endsection
