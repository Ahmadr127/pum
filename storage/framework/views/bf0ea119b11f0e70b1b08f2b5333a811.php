

<?php $__env->startSection('title', 'Release PUM'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header -->
        <div class="px-4 py-3 bg-white border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Permintaan Menunggu Release</h2>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <form action="<?php echo e(route('pum-releases.index')); ?>" method="GET">
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
                        <a href="<?php echo e(route('pum-releases.index')); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-1.5 px-3 rounded text-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Status Legend -->
        <div class="bg-white px-4 py-2 border-b border-gray-200">
            <div class="flex flex-wrap items-center gap-4 text-xs">
                <span class="font-medium text-gray-600">KETERANGAN:</span>
                <span class="inline-flex items-center">
                    <i class="fas fa-times-circle text-red-500 mr-1"></i> Ditolak [<?php echo e($summary['rejected']); ?>]
                </span>

                <span class="inline-flex items-center">
                    <i class="fas fa-clock text-blue-500 mr-1"></i> Menunggu [<?php echo e($summary['pending']); ?>]
                </span>
                <span class="inline-flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i> Selesai/Terpenuhi [<?php echo e($summary['fulfilled'] + $summary['approved']); ?>]
                </span>

            </div>
        </div>

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
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <?php echo $__env->make('pum.requests.columns.no', ['requests' => $requests, 'loop' => $loop], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.kode', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($request->requester->name ?? '-'); ?>

                        </td>
                        <?php echo $__env->make('pum.requests.columns.tanggal', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.jumlah', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.keterangan', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.progress', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <?php
                                $userApproval = $request->approvals->where('approver_id', auth()->id())->first();
                                $hasActioned = $userApproval && in_array($userApproval->status, ['approved', 'rejected']);
                            ?>

                            <?php if($hasActioned): ?>
                                <?php if($userApproval->status === 'approved'): ?>
                                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium border border-green-200">
                                        <i class="fas fa-check-circle mr-1"></i> Disetujui
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium border border-red-200">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                <?php endif; ?>
                            <?php elseif($request->canBeApprovedBy(auth()->user())): ?>
                                <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium border border-yellow-200">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Release Anda
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium border border-gray-200">
                                    <i class="fas fa-hourglass-half mr-1"></i> Menunggu
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="<?php echo e(route('pum-requests.show', $request)); ?>" 
                                   class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs hover:bg-indigo-200"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-check-circle text-4xl mb-2 text-green-300"></i>
                            <p>Tidak ada permintaan yang menunggu release dari Anda.</p>
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Pemrograman\magang\pum\resources\views/pum/releases/index.blade.php ENDPATH**/ ?>