
<div x-data="hasilScanModal()"
     x-show="open"
     x-on:scan-result.window="receive($event.detail)"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.55);">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col"
         @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-600 to-emerald-500 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-check-double text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Hasil Scan PDF</h3>
                    <p class="text-xs text-emerald-100">Periksa dan edit jika diperlukan sebelum menyimpan</p>
                </div>
            </div>
            <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        
        <div class="overflow-y-auto flex-1">
            <div class="p-6 space-y-4">

                
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-2.5 text-sm text-emerald-800">
                    <i class="fas fa-file-pdf text-emerald-500 text-lg shrink-0"></i>
                    <div class="min-w-0">
                        <span class="font-medium">Data berhasil dibaca dari PDF.</span>
                        <span class="text-emerald-600 ml-1" x-text="fileName"></span>
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        No. Surat / Kode Referensi
                    </label>
                    <input type="text" x-model="form.no_surat" name_preview="no_surat"
                           placeholder="Contoh: 00440/ADV/PNJ/02/2026"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pengaju <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2 items-center">
                        <select x-model="form.requester_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Pilih Pengaju --</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Terbaca dari PDF: <span class="italic font-medium" x-text="scannedRequesterName || '(tidak ditemukan)'"></span></p>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Pengajuan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" x-model="form.request_date"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Pengajuan (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium select-none">Rp</span>
                        <input type="number" x-model="form.amount" min="0" step="1000"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Keperluan</label>
                    <textarea x-model="form.description" rows="3"
                              placeholder="Keterangan keperluan pengajuan..."
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
                </div>

                
                <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-lg px-4 py-2.5 text-xs text-blue-700">
                    <i class="fas fa-paperclip text-blue-400 shrink-0"></i>
                    <span>File PDF yang anda scan akan otomatis tersimpan sebagai <strong>lampiran</strong> pengajuan ini.</span>
                </div>

                
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" x-model="form.submit_for_approval"
                           class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm text-gray-700">
                        <span class="font-medium">Langsung ajukan</span> untuk persetujuan setelah disimpan
                    </span>
                </label>

                
                <div x-show="errorMsg" class="flex items-center gap-2 text-red-600 text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <i class="fas fa-exclamation-circle shrink-0"></i>
                    <span x-text="errorMsg"></span>
                </div>

            </div>
        </div>

        
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 shrink-0">
            <button @click="open = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                Batal
            </button>
            <button @click="submitForm()"
                    :disabled="submitting"
                    :class="submitting ? 'opacity-70 cursor-not-allowed' : 'hover:bg-emerald-700'"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <i class="fas fa-spinner animate-spin" x-show="submitting"></i>
                <i class="fas fa-save" x-show="!submitting"></i>
                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Pengajuan'"></span>
            </button>
        </div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('757a395a-a38f-4d0a-a4bb-1da4d28b8875')): $__env->markAsRenderedOnce('757a395a-a38f-4d0a-a4bb-1da4d28b8875'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
function hasilScanModal() {
    return {
        open: false,
        submitting: false,
        errorMsg: '',
        fileName: '',
        scannedRequesterName: '',
        scannedFile: null,

        form: {
            no_surat: '',
            requester_id: '',
            request_date: '',
            amount: '',
            description: '',
            submit_for_approval: false,
        },

        receive(detail) {
            this.scannedRequesterName = detail.requester_name || '';
            this.scannedFile = detail.scannedFile || null;
            this.fileName = this.scannedFile ? this.scannedFile.name : '';
            this.errorMsg = '';

            // Pre-fill form
            this.form.no_surat     = detail.no_surat || '';
            this.form.request_date = detail.request_date || '';
            this.form.amount       = detail.amount || '';
            this.form.description  = detail.description || '';
            this.form.submit_for_approval = false;

            // Auto-match requester by name (case-insensitive partial)
            this.autoMatchRequester(this.scannedRequesterName);

            this.open = true;
        },

        autoMatchRequester(name) {
            if (!name) { this.form.requester_id = ''; return; }
            const nameLower = name.toLowerCase();
            const select = document.querySelector('[x-model="form.requester_id"]');
            if (!select) return;

            let bestOption = null;
            for (const opt of select.options) {
                if (opt.value && opt.text.toLowerCase().includes(nameLower.slice(0, 10))) {
                    bestOption = opt;
                    break;
                }
            }
            this.form.requester_id = bestOption ? bestOption.value : '';
        },

        async submitForm() {
            this.errorMsg = '';

            // Basic validation
            if (!this.form.requester_id) {
                this.errorMsg = 'Pilih pengaju terlebih dahulu.';
                return;
            }
            if (!this.form.request_date) {
                this.errorMsg = 'Tanggal pengajuan harus diisi.';
                return;
            }
            if (!this.form.amount || Number(this.form.amount) <= 0) {
                this.errorMsg = 'Jumlah pengajuan harus lebih dari 0.';
                return;
            }

            this.submitting = true;

            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            fd.append('requester_id', this.form.requester_id);
            fd.append('no_surat', this.form.no_surat);
            fd.append('request_date', this.form.request_date);
            fd.append('amount', this.form.amount);
            fd.append('description', this.form.description);
            fd.append('submit_for_approval', this.form.submit_for_approval ? '1' : '0');

            if (this.scannedFile) {
                fd.append('scanned_pdf', this.scannedFile);
            }

            try {
                const response = await fetch('<?php echo e(route("pum-requests.store")); ?>', {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    redirect: 'follow',
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                if (response.ok) {
                    window.location.reload();
                } else {
                    const text = await response.text();
                    // Try to extract Laravel validation error
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(text, 'text/html');
                    const errEl = doc.querySelector('.text-red-600, [class*="text-red"]');
                    this.errorMsg = errEl ? errEl.textContent.trim() : 'Terjadi kesalahan saat menyimpan data.';
                }
            } catch (err) {
                console.error(err);
                this.errorMsg = 'Gagal terhubung ke server. Silakan coba lagi.';
            } finally {
                this.submitting = false;
            }
        },
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH D:\Pemrograman\magang\pum\resources\views/pum/requests/modals/modal-hasilscan.blade.php ENDPATH**/ ?>