

<?php $__env->startSection('title', 'Buat Pengajuan Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="max-w-full px-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Buat Pengajuan Baru</h2>
                        <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah ini untuk mengajukan Permintaan Uang Muka.</p>
                    </div>
                </div>

                <form action="<?php echo e(route('pum-requests.store')); ?>" method="POST" id="pumRequestForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Section 1: Informasi Dasar & Keuangan -->
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Informasi Pengajuan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Pengaju -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Pengaju</label>
                                <input type="hidden" name="requester_id" value="<?php echo e(auth()->id()); ?>">
                                <div class="flex items-center px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-sm">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold mr-3 text-xs">
                                        <?php echo e(substr(auth()->user()->name, 0, 2)); ?>

                                    </div>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e(auth()->user()->name); ?></span>
                                </div>
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label for="request_date" class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="request_date" id="request_date" 
                                       value="<?php echo e(old('request_date', date('Y-m-d'))); ?>"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all">
                                <?php $__errorArgs = ['request_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase">Kategori <span class="text-red-500">*</span></label>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="procurement_category" value="barang_baru" 
                                               <?php echo e(old('procurement_category') === 'barang_baru' ? 'checked' : ''); ?> required 
                                               class="form-radio text-indigo-600 h-4 w-4 focus:ring-indigo-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Barang Baru</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="procurement_category" value="peremajaan" 
                                               <?php echo e(old('procurement_category') === 'peremajaan' ? 'checked' : ''); ?> required 
                                               class="form-radio text-indigo-600 h-4 w-4 focus:ring-indigo-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Peremajaan</span>
                                    </label>
                                </div>
                                <?php $__errorArgs = ['procurement_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Nominal & Keterangan -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Nominal (Emphasis) -->
                        <div class="lg:col-span-1 bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                            <label for="amount" class="block text-xs font-semibold text-indigo-600 mb-2 uppercase">Total Nominal <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <?php if (isset($component)) { $__componentOriginale843086d3d520ece4b5265a9f47dd634 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale843086d3d520ece4b5265a9f47dd634 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency-input','data' => ['name' => 'amount','value' => old('amount', ''),'label' => '','placeholder' => '0','required' => true,'class' => 'text-2xl font-bold text-indigo-700 bg-white border-indigo-200 focus:border-indigo-500 focus:ring-indigo-200 rounded-lg py-3 px-4 w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'amount','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('amount', '')),'label' => '','placeholder' => '0','required' => true,'class' => 'text-2xl font-bold text-indigo-700 bg-white border-indigo-200 focus:border-indigo-500 focus:ring-indigo-200 rounded-lg py-3 px-4 w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale843086d3d520ece4b5265a9f47dd634)): ?>
<?php $attributes = $__attributesOriginale843086d3d520ece4b5265a9f47dd634; ?>
<?php unset($__attributesOriginale843086d3d520ece4b5265a9f47dd634); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale843086d3d520ece4b5265a9f47dd634)): ?>
<?php $component = $__componentOriginale843086d3d520ece4b5265a9f47dd634; ?>
<?php unset($__componentOriginale843086d3d520ece4b5265a9f47dd634); ?>
<?php endif; ?>
                            </div>
                            <p class="text-xs text-indigo-400 mt-2">Masukkan nominal sesuai estimasi kebutuhan.</p>
                            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Keterangan -->
                        <div class="lg:col-span-2">
                            <label for="description" class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Keterangan / Keperluan</label>
                            <textarea name="description" id="description" rows="5"
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all resize-none"
                                      placeholder="Jelaskan secara rinci tujuan permintaan uang muka ini..."><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Section 3: Lampiran -->
                    <div class="border-t border-gray-100 pt-6 mb-8">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Dokumen Pendukung</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Lampiran 1 -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase">Lampiran Utama (Dokumen/Invoice)</label>
                                
                                <div x-data="fileUpload('attachments')" class="w-full">
                                    <div class="relative flex items-center justify-center w-full">
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
                                               :class="{ 'border-indigo-500 bg-indigo-50': dragover }"
                                               @dragover.prevent="dragover = true"
                                               @dragleave.prevent="dragover = false"
                                               @drop.prevent="handleDrop($event)">
                                            
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="files.length === 0">
                                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                                <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                                                <p class="text-xs text-gray-500">PDF, DOC, XLS, JPG, PNG (Max 5MB)</p>
                                            </div>

                                            <div class="w-full px-4" x-show="files.length > 0">
                                                <template x-for="(file, index) in files" :key="index">
                                                    <div class="flex items-center justify-between p-2 mb-1 bg-white rounded border border-gray-200 shadow-sm text-sm">
                                                        <div class="flex items-center gap-2 overflow-hidden">
                                                            <i class="fas fa-file text-gray-400"></i>
                                                            <span class="truncate font-medium text-gray-700" x-text="file.name"></span>
                                                        </div>
                                                        <button type="button" @click="removeFile(index)" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                                                    </div>
                                                </template>
                                                <div class="text-center mt-2">
                                                    <span class="text-xs text-blue-600 font-medium">+ Tambah file lain</span>
                                                </div>
                                            </div>

                                            <input type="file" name="attachments[]" multiple x-ref="fileInput" class="hidden" @change="handleFiles($event)" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                        </label>
                                    </div>
                                </div>
                                <?php $__errorArgs = ['attachments'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Lampiran 2 -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase">Lampiran Tambahan (Opsional)</label>

                                <div x-data="fileUpload('attachments2')" class="w-full">
                                    <div class="relative flex items-center justify-center w-full">
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
                                               :class="{ 'border-indigo-500 bg-indigo-50': dragover }"
                                               @dragover.prevent="dragover = true"
                                               @dragleave.prevent="dragover = false"
                                               @drop.prevent="handleDrop($event)">
                                            
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="files.length === 0">
                                                <i class="fas fa-paperclip text-2xl text-gray-400 mb-2"></i>
                                                <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                                                <p class="text-xs text-gray-500">PDF, DOC, XLS, JPG, PNG (Max 5MB)</p>
                                            </div>

                                            <div class="w-full px-4" x-show="files.length > 0">
                                                <template x-for="(file, index) in files" :key="index">
                                                    <div class="flex items-center justify-between p-2 mb-1 bg-white rounded border border-gray-200 shadow-sm text-sm">
                                                        <div class="flex items-center gap-2 overflow-hidden">
                                                            <i class="fas fa-file text-gray-400"></i>
                                                            <span class="truncate font-medium text-gray-700" x-text="file.name"></span>
                                                        </div>
                                                        <button type="button" @click="removeFile(index)" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                                                    </div>
                                                </template>
                                                <div class="text-center mt-2">
                                                    <span class="text-xs text-blue-600 font-medium">+ Tambah file lain</span>
                                                </div>
                                            </div>

                                            <input type="file" name="attachments2[]" multiple x-ref="fileInput" class="hidden" @change="handleFiles($event)" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                        </label>
                                    </div>
                                </div>
                                <?php $__errorArgs = ['attachments2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="<?php echo e(route('pum-requests.index')); ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm">
                            Batal
                        </a>
                        <button type="submit" name="submit_for_approval" value="0" 
                                class="px-5 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium transition-colors text-sm shadow-sm">
                            Simpan Draft
                        </button>
                        <button type="submit" name="submit_for_approval" value="1" 
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors text-sm shadow-md hover:shadow-lg flex items-center gap-2">
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Simpan & Ajukan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
                if (file.size <= 5 * 1024 * 1024) { // 5MB limit
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Pemrograman\magang\pum\resources\views/pum/requests/create.blade.php ENDPATH**/ ?>