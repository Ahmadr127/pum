

<?php $__env->startSection('title', 'Buat Pengajuan Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-6 text-blue-600">BUAT PENGAJUAN BARU</h2>

            <form action="<?php echo e(route('pum-requests.store')); ?>" method="POST" id="pumRequestForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Pengaju -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                PENGAJU <span class="text-red-500">*</span>
                            </label>
                            <input type="hidden" name="requester_id" value="<?php echo e(auth()->id()); ?>">
                            <input type="text" 
                                   value="<?php echo e(auth()->user()->name); ?>" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 cursor-not-allowed"
                                   readonly>
                        </div>

                        <!-- Tanggal -->
                        <div>
                            <label for="request_date" class="block text-sm font-medium text-gray-700 mb-1">
                                TANGGAL <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="request_date" id="request_date" 
                                   value="<?php echo e(old('request_date', date('Y-m-d'))); ?>"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
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

                        <!-- Jumlah -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                JUMLAH <span class="text-red-500">*</span>
                            </label>
                            <?php if (isset($component)) { $__componentOriginale843086d3d520ece4b5265a9f47dd634 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale843086d3d520ece4b5265a9f47dd634 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency-input','data' => ['name' => 'amount','value' => old('amount', ''),'label' => '','placeholder' => '0','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'amount','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('amount', '')),'label' => '','placeholder' => '0','required' => true]); ?>
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

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">STATUS</label>
                            <div class="flex items-center gap-2 py-2">
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => 'new']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => 'new']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                            </div>
                        </div>

                        <!-- Workflow (Optional) -->
                        <?php if($workflows->count() > 1): ?>
                        <div>
                            <label for="workflow_id" class="block text-sm font-medium text-gray-700 mb-1">
                                WORKFLOW APPROVAL
                            </label>
                            <select name="workflow_id" id="workflow_id" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">Default</option>
                                <?php $__currentLoopData = $workflows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workflow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($workflow->id); ?>" <?php echo e($workflow->is_default ? 'selected' : ''); ?>>
                                        <?php echo e($workflow->name); ?> (<?php echo e($workflow->steps->count()); ?> step)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- Keterangan -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                KETERANGAN
                            </label>
                            <textarea name="description" id="description" rows="6"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                      placeholder="Masukkan keterangan atau deskripsi permintaan..."><?php echo e(old('description')); ?></textarea>
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

                    <!-- Right Column - Lampiran -->
                    <div class="space-y-4">
                        <!-- Lampiran 1 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                LAMPIRAN 1
                            </label>
                            <div x-data="fileUpload('attachments')" class="space-y-2">
                                <!-- Compact Upload Area -->
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-indigo-400 transition-colors cursor-pointer"
                                     @click="$refs.fileInput.click()"
                                     @dragover.prevent="dragover = true"
                                     @dragleave.prevent="dragover = false"
                                     @drop.prevent="handleDrop($event)"
                                     :class="{ 'border-indigo-400 bg-indigo-50': dragover }">
                                    <input type="file" name="attachments[]" multiple x-ref="fileInput" 
                                           class="hidden" @change="handleFiles($event)"
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                        <div class="text-left">
                                            <p class="text-sm text-gray-600">Klik atau drag file ke sini</p>
                                            <p class="text-xs text-gray-400">PDF, DOC, XLS, JPG, PNG (Max 5MB)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- File List -->
                                <div class="space-y-1 max-h-32 overflow-y-auto" x-show="files.length > 0">
                                    <template x-for="(file, index) in files" :key="index">
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                                <i class="fas fa-file text-gray-400 text-sm"></i>
                                                <span class="truncate" x-text="file.name"></span>
                                                <span class="text-gray-400" x-text="formatSize(file.size)"></span>
                                            </div>
                                            <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </div>
                                    </template>
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
                                <?php $__errorArgs = ['attachments.*'];
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

                        <!-- Lampiran 2 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                LAMPIRAN 2
                            </label>
                            <div x-data="fileUpload('attachments2')" class="space-y-2">
                                <!-- Compact Upload Area -->
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-indigo-400 transition-colors cursor-pointer"
                                     @click="$refs.fileInput.click()"
                                     @dragover.prevent="dragover = true"
                                     @dragleave.prevent="dragover = false"
                                     @drop.prevent="handleDrop($event)"
                                     :class="{ 'border-indigo-400 bg-indigo-50': dragover }">
                                    <input type="file" name="attachments2[]" multiple x-ref="fileInput" 
                                           class="hidden" @change="handleFiles($event)"
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                        <div class="text-left">
                                            <p class="text-sm text-gray-600">Klik atau drag file ke sini</p>
                                            <p class="text-xs text-gray-400">PDF, DOC, XLS, JPG, PNG (Max 5MB)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- File List -->
                                <div class="space-y-1 max-h-32 overflow-y-auto" x-show="files.length > 0">
                                    <template x-for="(file, index) in files" :key="index">
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                                <i class="fas fa-file text-gray-400 text-sm"></i>
                                                <span class="truncate" x-text="file.name"></span>
                                                <span class="text-gray-400" x-text="formatSize(file.size)"></span>
                                            </div>
                                            <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </div>
                                    </template>
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
                                <?php $__errorArgs = ['attachments2.*'];
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
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
                    <a href="<?php echo e(route('pum-requests.index')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium">
                        Batal
                    </a>
                    <button type="submit" name="submit_for_approval" value="0" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium">
                        Simpan Draft
                    </button>
                    <button type="submit" name="submit_for_approval" value="1" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                        <i class="fas fa-paper-plane mr-1"></i>
                        SIMPAN & AJUKAN
                    </button>
                </div>
            </form>
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