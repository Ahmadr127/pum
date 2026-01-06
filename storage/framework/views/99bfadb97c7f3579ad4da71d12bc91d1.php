<?php $__env->startSection('title', 'Approval Permintaan Uang Muka'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header -->
        <div class="px-4 py-3 bg-white border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Permintaan Menunggu Persetujuan Anda</h2>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <form action="<?php echo e(route('pum-approvals.index')); ?>" method="GET">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="w-36">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" 
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>
                    <div class="w-36">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" 
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pencarian</label>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Kode, nama pengaju..."
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-1.5 px-3 rounded text-sm">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                        <a href="<?php echo e(route('pum-approvals.index')); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-1.5 px-3 rounded text-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pengaju</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e(($requests->currentPage() - 1) * $requests->perPage() + $index + 1); ?>

                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <a href="<?php echo e(route('pum-requests.show', $request)); ?>" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                <?php echo e($request->code); ?>

                            </a>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($request->requester->name ?? '-'); ?>

                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($request->request_date->format('d/m/Y')); ?>

                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                            Rp <?php echo e(number_format($request->amount, 0, ',', '.')); ?>

                        </td>
                        <td class="px-3 py-2 text-sm text-gray-900 max-w-xs truncate">
                            <?php echo e(Str::limit($request->description, 30) ?? '-'); ?>

                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="<?php echo e(route('pum-requests.show', $request)); ?>" 
                                   class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs hover:bg-indigo-200">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                
                                <?php
                                    $userApproval = $request->approvals->where('approver_id', auth()->id())->first();
                                    $hasActioned = $userApproval && in_array($userApproval->status, ['approved', 'rejected']);
                                ?>
                                
                                <?php if(!$hasActioned && $request->canBeApprovedBy(auth()->user())): ?>
                                    
                                    <button 
                                        type="button" 
                                        @click="$dispatch('open-quick-approve', { id: <?php echo e($request->id); ?>, code: '<?php echo e($request->code); ?>', amount: <?php echo e($request->amount); ?> })"
                                        class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded text-xs hover:bg-green-200"
                                    >
                                        <i class="fas fa-check mr-1"></i> Approve
                                    </button>
                                    <button 
                                        type="button"
                                        @click="$dispatch('open-quick-reject', { id: <?php echo e($request->id); ?>, code: '<?php echo e($request->code); ?>' })"
                                        class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200"
                                    >
                                        <i class="fas fa-times mr-1"></i> Reject
                                    </button>
                                <?php elseif($hasActioned): ?>
                                    
                                    <?php if($userApproval->status === 'approved'): ?>
                                        <span class="inline-flex items-center px-2 py-1 bg-green-50 text-green-700 rounded text-xs border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Approve
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-1 bg-red-50 text-red-700 rounded text-xs border border-red-200">
                                            <i class="fas fa-times-circle mr-1"></i> Sudah Reject
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-check-circle text-4xl mb-2 text-green-300"></i>
                            <p>Tidak ada permintaan yang menunggu persetujuan Anda.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($requests->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-200">
            <?php echo e($requests->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>


<?php if (isset($component)) { $__componentOriginal12676ed3d863d220f39ba739b04e436c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal12676ed3d863d220f39ba739b04e436c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pum.approve-modal','data' => ['action' => '','modalName' => 'quick-approve-modal','showDetails' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pum.approve-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => '','modal-name' => 'quick-approve-modal','show-details' => true]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pum.reject-modal','data' => ['action' => '','modalName' => 'quick-reject-modal','showDetails' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pum.reject-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => '','modal-name' => 'quick-reject-modal','show-details' => true]); ?>
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

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/pum/approvals/index.blade.php ENDPATH**/ ?>