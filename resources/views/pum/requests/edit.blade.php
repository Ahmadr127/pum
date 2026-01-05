@extends('layouts.app')

@section('title', 'Edit Permintaan - ' . $pumRequest->code)

@section('content')
<div class="w-full">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 text-blue-600">EDIT PENGAJUAN</h2>
                    <p class="text-sm text-gray-500">{{ $pumRequest->code }}</p>
                </div>
                <x-status-badge :status="$pumRequest->status" />
            </div>

            <form action="{{ route('pum-requests.update', $pumRequest) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Pengaju -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                PENGAJU <span class="text-red-500">*</span>
                            </label>
                            <input type="hidden" name="requester_id" value="{{ $pumRequest->requester_id }}">
                            <input type="text" 
                                   value="{{ $pumRequest->requester->name ?? '-' }}" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 cursor-not-allowed"
                                   readonly>
                        </div>

                        <!-- Tanggal -->
                        <div>
                            <label for="request_date" class="block text-sm font-medium text-gray-700 mb-1">
                                TANGGAL <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="request_date" id="request_date" 
                                   value="{{ old('request_date', $pumRequest->request_date->format('Y-m-d')) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @error('request_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jumlah -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                JUMLAH <span class="text-red-500">*</span>
                            </label>
                            <x-currency-input 
                                name="amount" 
                                :value="old('amount', $pumRequest->amount)" 
                                label=""
                                placeholder="0"
                                required
                            />
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Workflow (Optional) -->
                        @if($workflows->count() > 1)
                        <div>
                            <label for="workflow_id" class="block text-sm font-medium text-gray-700 mb-1">
                                WORKFLOW APPROVAL
                            </label>
                            <select name="workflow_id" id="workflow_id" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">Default</option>
                                @foreach($workflows as $workflow)
                                    <option value="{{ $workflow->id }}" {{ old('workflow_id', $pumRequest->workflow_id) == $workflow->id ? 'selected' : '' }}>
                                        {{ $workflow->name }} ({{ $workflow->steps->count() }} step)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>

                    <!-- Middle Column - Keterangan -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            KETERANGAN
                        </label>
                        <textarea name="description" id="description" rows="12"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                  placeholder="Masukkan keterangan atau deskripsi permintaan...">{{ old('description', $pumRequest->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Right Column - Lampiran -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            LAMPIRAN
                        </label>
                        
                        <!-- Existing Attachments -->
                        @if($pumRequest->attachments && count($pumRequest->attachments) > 0)
                        <div class="mb-3 space-y-2">
                            <p class="text-xs text-gray-500 font-medium">File yang sudah diupload:</p>
                            @foreach($pumRequest->attachments as $index => $attachment)
                            <div class="flex items-center justify-between p-2 bg-blue-50 rounded-lg text-sm">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <i class="fas fa-file text-blue-400"></i>
                                    <a href="{{ Storage::url($attachment) }}" target="_blank" class="truncate text-blue-600 hover:underline">
                                        {{ basename($attachment) }}
                                    </a>
                                </div>
                                <label class="flex items-center gap-1 text-red-500 cursor-pointer">
                                    <input type="checkbox" name="remove_attachments[]" value="{{ $index }}" class="rounded">
                                    <span class="text-xs">Hapus</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div x-data="fileUpload()" class="space-y-3">
                            <!-- Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-400 transition-colors cursor-pointer"
                                 @click="$refs.fileInput.click()"
                                 @dragover.prevent="dragover = true"
                                 @dragleave.prevent="dragover = false"
                                 @drop.prevent="handleDrop($event)"
                                 :class="{ 'border-indigo-400 bg-indigo-50': dragover }">
                                <input type="file" name="attachments[]" multiple x-ref="fileInput" 
                                       class="hidden" @change="handleFiles($event)"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Tambah file baru</p>
                                <p class="text-xs text-gray-400 mt-1">PDF, DOC, XLS, JPG, PNG (Max 5MB)</p>
                            </div>

                            <!-- File List -->
                            <div class="space-y-2 max-h-32 overflow-y-auto">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg text-sm">
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <i class="fas fa-file text-gray-400"></i>
                                            <span class="truncate" x-text="file.name"></span>
                                            <span class="text-xs text-gray-400" x-text="formatSize(file.size)"></span>
                                        </div>
                                        <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('attachments')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
                    <a href="{{ route('pum-requests.show', $pumRequest) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium">
                        Batal
                    </a>
                    <button type="submit" name="submit_for_approval" value="0" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium">
                        Simpan Draft
                    </button>
                    <button type="submit" name="submit_for_approval" value="1" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Simpan & Ajukan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function fileUpload() {
    return {
        files: [],
        dragover: false,
        
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
