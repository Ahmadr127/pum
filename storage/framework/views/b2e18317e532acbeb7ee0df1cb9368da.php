<?php $__env->startSection('title', 'Detail Permintaan - ' . $pumRequest->code); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="max-w-full px-6">
        <!-- Flash Messages -->
        <?php if(session('success')): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3" role="alert">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <span class="block sm:inline font-medium"><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3" role="alert">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                <span class="block sm:inline font-medium"><?php echo e(session('error')); ?></span>
            </div>
        <?php endif; ?>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column: Main Content -->
            <div class="flex-1 min-w-0 space-y-6">
                <!-- Detail Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Permintaan</h1>
                                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                                    <i class="fas fa-hashtag text-gray-300"></i>
                                    <span class="font-mono"><?php echo e($pumRequest->code); ?></span>
                                    <span class="text-gray-300">|</span>
                                    <i class="fas fa-calendar-alt text-gray-300"></i>
                                    <span><?php echo e($pumRequest->request_date->format('d M Y')); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pengaju</label>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        <?php echo e(substr($pumRequest->requester->name ?? 'User', 0, 2)); ?>

                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900"><?php echo e($pumRequest->requester->name ?? '-'); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo e($pumRequest->requester->organizationUnit->name ?? 'Unit tidak diketahui'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Nominal</label>
                                <p class="text-2xl font-bold text-indigo-600">Rp <?php echo e(number_format($pumRequest->amount, 0, ',', '.')); ?></p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Kategori: <span class="font-medium text-gray-700 capitalize"><?php echo e(str_replace('_', ' ', $pumRequest->procurement_category)); ?></span>
                                </p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Keterangan / Keperluan</label>
                            <div class="prose prose-sm max-w-none bg-white text-gray-700 p-0">
                                <?php if($pumRequest->description): ?>
                                    <p class="whitespace-pre-line leading-relaxed text-gray-600"><?php echo e($pumRequest->description); ?></p>
                                <?php else: ?>
                                    <p class="italic text-gray-400">Tidak ada keterangan lampiran.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="mb-6 border-t border-gray-100 pt-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Dokumen Lampiran</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Lampiran Utama -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-2 uppercase">Lampiran Utama</label>
                                    <?php if($pumRequest->attachments && count($pumRequest->attachments) > 0): ?>
                                        <div class="space-y-2">
                                            <?php $__currentLoopData = $pumRequest->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(Storage::url($attachment)); ?>" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                                <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-indigo-500 shadow-sm">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <span class="text-sm text-gray-700 group-hover:text-indigo-700 truncate flex-1 font-medium"><?php echo e(basename($attachment)); ?></span>
                                                <i class="fas fa-external-link-alt text-gray-300 group-hover:text-indigo-400 text-xs"></i>
                                            </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-400 italic">Tidak ada lampiran utama.</p>
                                    <?php endif; ?>
                                </div>

                                <!-- Lampiran Tambahan -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-2 uppercase">Lampiran Tambahan</label>
                                    <?php if($pumRequest->attachments2 && count($pumRequest->attachments2) > 0): ?>
                                        <div class="space-y-2">
                                            <?php $__currentLoopData = $pumRequest->attachments2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(Storage::url($attachment)); ?>" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                                <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-indigo-500 shadow-sm">
                                                    <i class="fas fa-paperclip"></i>
                                                </div>
                                                <span class="text-sm text-gray-700 group-hover:text-indigo-700 truncate flex-1 font-medium"><?php echo e(basename($attachment)); ?></span>
                                                <i class="fas fa-external-link-alt text-gray-300 group-hover:text-indigo-400 text-xs"></i>
                                            </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-400 italic">Tidak ada lampiran tambahan.</p>
                                    <?php endif; ?>
                                </div>

                                </div>
                            </div>
                            
                            <!-- Dokumen FS -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <label class="block text-xs font-medium text-gray-500 mb-2 uppercase">Dokumen FS</label>
                                <?php
                                    // Collect all approvals with FS forms
                                    $fsApprovals = $pumRequest->approvals->whereNotNull('fs_form_path');
                                ?>
                                
                                <?php if($fsApprovals->count() > 0): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <?php $__currentLoopData = $fsApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(Storage::url($approval->fs_form_path)); ?>" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                            <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-indigo-500 shadow-sm shrink-0">
                                                <i class="fas fa-file-contract"></i>
                                            </div>
                                            <span class="text-sm text-gray-700 group-hover:text-indigo-700 truncate flex-1 font-medium" title="<?php echo e(basename($approval->fs_form_path)); ?>"><?php echo e(basename($approval->fs_form_path)); ?></span>
                                            <i class="fas fa-external-link-alt text-gray-300 group-hover:text-indigo-400 text-xs"></i>
                                        </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center gap-2 text-sm text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <i class="fas fa-info-circle text-gray-300"></i>
                                        Tidak ada dokumen FS yang diunggah.
                                    </div>
                                <?php endif; ?>
                            </div>


                        <!-- Metadata Footer -->
                        <div class="border-t border-gray-100 pt-4 flex flex-wrap gap-4 text-xs text-gray-400">
                            <span>Dibuat oleh: <span class="text-gray-600"><?php echo e($pumRequest->creator->name ?? '-'); ?></span></span>
                            <span>&bull;</span>
                            <span>Waktu: <?php echo e($pumRequest->created_at->format('d/m/Y H:i')); ?></span>
                            <?php if($pumRequest->workflow): ?>
                                <span>&bull;</span>
                                <span>Workflow: <span class="text-gray-600"><?php echo e($pumRequest->workflow->name); ?></span></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Approval History -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Riwayat Persetujuan</h3>
                        <span class="text-xs bg-white border border-gray-200 px-2 py-1 rounded text-gray-500">
                            <?php echo e($pumRequest->approvals->count()); ?> Tahap
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahap</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approver</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $pumRequest->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="<?php echo e($approval->status === 'pending' && $pumRequest->current_step_order === $approval->step_order ? 'bg-indigo-50/50' : 'hover:bg-gray-50/50'); ?> transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium"><?php echo e($approval->step->name ?? '-'); ?></span>
                                                <?php if($approval->fs_form_path): ?>
                                                    <a href="<?php echo e(Storage::url($approval->fs_form_path)); ?>" target="_blank" 
                                                       class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs hover:bg-blue-100 transition-colors" 
                                                       title="Download FS Form">
                                                        <i class="fas fa-file-contract"></i>
                                                        <span class="max-w-[150px] truncate"><?php echo e(basename($approval->fs_form_path)); ?></span>
                                                        <i class="fas fa-download text-[10px]"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo e($approval->approver->name ?? '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($approval->status === 'approved'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1.5"></i> Disetujui
                                            </span>
                                        <?php elseif($approval->status === 'rejected'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1.5"></i> Ditolak
                                            </span>
                                        <?php elseif($approval->status === 'pending'): ?>
                                            <?php if($pumRequest->current_step_order === $approval->step_order): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 animate-pulse">
                                                    <i class="fas fa-clock mr-1.5"></i> Menunggu
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-hourglass-start mr-1.5"></i> Mengantri
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($approval->responded_at ? $approval->responded_at->format('d/m/Y H:i') : '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        <?php echo e($approval->notes ?? '-'); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                                        Belum ada data approval.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="w-full lg:w-[35%] space-y-6">
                <!-- Status & Actions Card -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-0">Status Pengajuan</h3>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $pumRequest->status,'size' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pumRequest->status),'size' => 'md']); ?>
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

                    <!-- Actions -->
                    <div class="space-y-4">
                        <?php if($pumRequest->status === 'new'): ?>
                            <a href="<?php echo e(route('pum-requests.edit', $pumRequest)); ?>" class="block w-full text-center px-4 py-2 bg-yellow-400 text-yellow-900 rounded-lg hover:bg-yellow-500 transition-colors font-medium text-sm shadow-sm group">
                                <i class="fas fa-edit mr-2 group-hover:scale-110 transition-transform"></i> Edit Data
                            </a>
                            
                            <form action="<?php echo e(route('pum-requests.submit', $pumRequest)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full justify-center flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm shadow-md group">
                                    <i class="fas fa-paper-plane mr-2 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 transition-transform"></i> Ajukan Sekarang
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if($canApprove): ?>
                            <div class="border-t border-gray-100 pt-4 mt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Form Persetujuan</h4>
                                
                                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                                    <?php echo csrf_field(); ?>
                                    
                                    <!-- Notes -->
                                    <div>
                                        <label for="notes" class="block text-xs font-medium text-gray-500 mb-1">Catatan (Opsional saat Menyetujui, Wajib saat Menolak)</label>
                                        <textarea name="notes" id="notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm resize-none placeholder-gray-400" placeholder="Tulis catatan persetujuan/penolakan..."></textarea>
                                        <?php $__errorArgs = ['notes'];
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

                                    <!-- FS Form Upload (Conditional) -->
                                    <?php
                                        $currentStep = $pumRequest->getCurrentStep();
                                    ?>
                                    
                                    <?php if($currentStep && $currentStep->is_upload_fs_required): ?>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Upload Dokumen FS <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="file" name="fs_form" id="fs_form" required class="block w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-1">Wajib upload form FS untuk tahap ini.</p>
                                            <?php $__errorArgs = ['fs_form'];
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
                                    <?php endif; ?>

                                    <!-- Action Buttons -->
                                    <div class="grid grid-cols-2 gap-3 pt-2">
                                        <button type="submit" formaction="<?php echo e(route('pum-requests.reject', $pumRequest)); ?>" class="flex items-center justify-center px-3 py-2 bg-white border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors font-medium text-sm shadow-sm">
                                            <i class="fas fa-times mr-2"></i> Tolak
                                        </button>
                                        <button type="submit" formaction="<?php echo e(route('pum-requests.approve', $pumRequest)); ?>" class="flex items-center justify-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm shadow-sm">
                                            <i class="fas fa-check mr-2"></i> Setujui
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($pumRequest->status === 'new' || $pumRequest->status === 'draft'): ?>
                             <!-- Optional: Delete button if needed -->
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Pemrograman\magang\pum\resources\views/pum/requests/show.blade.php ENDPATH**/ ?>