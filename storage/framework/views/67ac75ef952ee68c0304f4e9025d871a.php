<?php $__env->startSection('title', 'Detail Permintaan - ' . $pumRequest->code); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Flash Messages -->
    <?php if(session('success')): ?>
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">DETAIL PERMINTAAN UANG MUKA</h2>
                    <p class="text-sm text-gray-500 mt-1"><?php echo e($pumRequest->code); ?></p>
                </div>
                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $pumRequest->status,'size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pumRequest->status),'size' => 'lg']); ?>
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

            <!-- Request Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">PENGAJU</label>
                        <p class="text-gray-900 font-medium text-lg"><?php echo e($pumRequest->requester->name ?? '-'); ?></p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">TANGGAL</label>
                            <p class="text-gray-900"><?php echo e($pumRequest->request_date->format('d M Y')); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">DIBUAT OLEH</label>
                            <p class="text-gray-900"><?php echo e($pumRequest->creator->name ?? '-'); ?></p>
                            <p class="text-gray-500 text-xs"><?php echo e($pumRequest->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">JUMLAH DIAJUKAN</label>
                        <p class="text-gray-900 font-bold text-2xl text-green-600">Rp <?php echo e(number_format($pumRequest->amount, 0, ',', '.')); ?></p>
                    </div>

                    <?php if($pumRequest->workflow): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">WORKFLOW APPROVAL</label>
                        <p class="text-gray-900"><?php echo e($pumRequest->workflow->name); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Middle Column - Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">KETERANGAN</label>
                    <div class="bg-gray-50 p-4 rounded-md min-h-32 text-gray-900">
                        <?php echo e($pumRequest->description ?? 'Tidak ada keterangan'); ?>

                    </div>
                </div>

                <!-- Right Column - Lampiran -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">LAMPIRAN</label>
                    <?php if($pumRequest->attachments && count($pumRequest->attachments) > 0): ?>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $pumRequest->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(Storage::url($attachment)); ?>" target="_blank" 
                           class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <?php
                                $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                $iconClass = match(strtolower($ext)) {
                                    'pdf' => 'fa-file-pdf text-red-500',
                                    'doc', 'docx' => 'fa-file-word text-blue-500',
                                    'xls', 'xlsx' => 'fa-file-excel text-green-500',
                                    'jpg', 'jpeg', 'png' => 'fa-file-image text-purple-500',
                                    default => 'fa-file text-gray-500'
                                };
                            ?>
                            <i class="fas <?php echo e($iconClass); ?> text-lg"></i>
                            <span class="text-sm text-gray-700 flex-1 truncate"><?php echo e(basename($attachment)); ?></span>
                            <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="bg-gray-50 p-4 rounded-md text-center text-gray-400">
                        <i class="fas fa-paperclip text-2xl mb-2"></i>
                        <p class="text-sm">Tidak ada lampiran</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-200 pt-6">
                <?php if($pumRequest->status === 'new'): ?>
                    <a href="<?php echo e(route('pum-requests.edit', $pumRequest)); ?>" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 font-medium">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    
                    <form action="<?php echo e(route('pum-requests.submit', $pumRequest)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                            <i class="fas fa-paper-plane mr-1"></i> Ajukan Persetujuan
                        </button>
                    </form>
                <?php endif; ?>

                <?php if($canApprove): ?>
                    <button 
                        type="button" 
                        @click="$dispatch('open-modal', 'approve-modal')" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium"
                    >
                        <i class="fas fa-check mr-1"></i> Setujui
                    </button>
                    
                    <button 
                        type="button" 
                        @click="$dispatch('open-modal', 'reject-modal')" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium"
                    >
                        <i class="fas fa-times mr-1"></i> Tolak
                    </button>
                <?php endif; ?>

                <?php if($pumRequest->status === 'approved'): ?>
                    <form action="<?php echo e(route('pum-requests.fulfill', $pumRequest)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 font-medium"
                                onclick="return confirm('Tandai permintaan ini sebagai terpenuhi?')">
                            <i class="fas fa-check-double mr-1"></i> Tandai Terpenuhi
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if(auth()->id() == $pumRequest->requester_id || auth()->id() == $pumRequest->created_by): ?>
        <div class="p-6 bg-gray-50">
            <h3 class="text-sm font-medium text-blue-600 mb-4">Riwayat Persetujuan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Step</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disetujui Oleh</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $pumRequest->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($approval->status === 'pending' && $pumRequest->current_step_order === $approval->step_order ? 'bg-yellow-50' : ''); ?>">
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                <?php echo e($approval->responded_at ? $approval->responded_at->format('d/m/Y H:i') : '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span class="font-medium"><?php echo e($approval->step->name ?? '-'); ?></span>
                                <?php if($pumRequest->current_step_order === $approval->step_order && $approval->status === 'pending'): ?>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-arrow-right mr-1"></i> Tahap Ini
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">
                                <?php echo e($approval->notes ?? '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                <?php echo e($approval->approver->name ?? '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if($approval->status === 'approved'): ?>
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </span>
                                <?php elseif($approval->status === 'rejected'): ?>
                                    <span class="inline-flex items-center text-red-600">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center text-gray-400">
                                        <i class="fas fa-clock text-lg"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-400 text-sm">
                                Belum ada riwayat persetujuan
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<?php if($canApprove): ?>
<?php if (isset($component)) { $__componentOriginal12676ed3d863d220f39ba739b04e436c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal12676ed3d863d220f39ba739b04e436c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pum.approve-modal','data' => ['action' => route('pum-requests.approve', $pumRequest)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pum.approve-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('pum-requests.approve', $pumRequest))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal12676ed3d863d220f39ba739b04e436c)): ?>
<?php $attributes = $__attributesOriginal12676ed3d863d220f39ba739b04e436c; ?>
<?php unset($__attributesOriginal12676ed3d863d220f39ba739b04e436c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal12676ed3d863d220f39ba739b04e436c)): ?>
<?php $component = $__componentOriginal12676ed3d863d220f39ba739b04e436c; ?>
<?php unset($__componentOriginal12676ed3d863d220f39ba739b04e436c); ?>
<?php endif; ?>
<?php if (isset($component)) { $__componentOriginalee330e19988b2e7820d43252c533cd9a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee330e19988b2e7820d43252c533cd9a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pum.reject-modal','data' => ['action' => route('pum-requests.reject', $pumRequest)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pum.reject-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('pum-requests.reject', $pumRequest))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee330e19988b2e7820d43252c533cd9a)): ?>
<?php $attributes = $__attributesOriginalee330e19988b2e7820d43252c533cd9a; ?>
<?php unset($__attributesOriginalee330e19988b2e7820d43252c533cd9a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee330e19988b2e7820d43252c533cd9a)): ?>
<?php $component = $__componentOriginalee330e19988b2e7820d43252c533cd9a; ?>
<?php unset($__componentOriginalee330e19988b2e7820d43252c533cd9a); ?>
<?php endif; ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/pum/requests/show.blade.php ENDPATH**/ ?>