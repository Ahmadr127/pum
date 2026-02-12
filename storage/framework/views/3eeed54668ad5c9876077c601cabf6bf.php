

<?php $__env->startSection('title', 'Permintaan Uang Muka'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header with Add Button -->
        <div class="px-4 py-3 bg-white border-b border-gray-200 flex justify-end">
            <a href="<?php echo e(route('pum-requests.create')); ?>" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded inline-flex items-center text-sm">
                <i class="fas fa-plus mr-2"></i>
                Tambah Pengajuan
            </a>
        </div>

        <!-- Filters - Single Row -->
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <form action="<?php echo e(route('pum-requests.my-requests')); ?>" method="GET">
                <div class="flex flex-wrap items-end gap-3">
                    <!-- Dari Tanggal -->
                    <div class="w-36">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" 
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="w-36">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" 
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>

                    <!-- Status - Searchable -->
                    <div class="w-44">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <?php if (isset($component)) { $__componentOriginal0728ba7b37b7eda62f62767c5dccebf3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0728ba7b37b7eda62f62767c5dccebf3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-dropdown','data' => ['name' => 'status','options' => collect($statuses)->map(fn($label, $key) => (object)['key' => $key, 'label' => $label]),'valueField' => 'key','labelField' => 'label','selected' => request('status'),'placeholder' => 'Semua','emptyOption' => 'Semua']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($statuses)->map(fn($label, $key) => (object)['key' => $key, 'label' => $label])),'value-field' => 'key','label-field' => 'label','selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('status')),'placeholder' => 'Semua','empty-option' => 'Semua']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0728ba7b37b7eda62f62767c5dccebf3)): ?>
<?php $attributes = $__attributesOriginal0728ba7b37b7eda62f62767c5dccebf3; ?>
<?php unset($__attributesOriginal0728ba7b37b7eda62f62767c5dccebf3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0728ba7b37b7eda62f62767c5dccebf3)): ?>
<?php $component = $__componentOriginal0728ba7b37b7eda62f62767c5dccebf3; ?>
<?php unset($__componentOriginal0728ba7b37b7eda62f62767c5dccebf3); ?>
<?php endif; ?>
                    </div>

                    <!-- Pencarian -->
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pencarian</label>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Kode, keterangan..."
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-1.5 px-3 rounded text-sm inline-flex items-center">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                        <a href="<?php echo e(route('pum-requests.my-requests')); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-1.5 px-3 rounded text-sm">
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
                    <i class="fas fa-times-circle text-red-500 mr-1"></i> Ditolak [<?php echo e($requests->where('status', 'rejected')->count()); ?>]
                </span>
                <span class="inline-flex items-center">
                    <i class="fas fa-file-alt text-yellow-500 mr-1"></i> Baru [<?php echo e($requests->where('status', 'new')->count()); ?>]
                </span>
                <span class="inline-flex items-center">
                    <i class="fas fa-clock text-blue-500 mr-1"></i> Menunggu [<?php echo e($requests->where('status', 'pending')->count()); ?>]
                </span>
                <span class="inline-flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i> Disetujui [<?php echo e($requests->where('status', 'approved')->count()); ?>]
                </span>
                <span class="inline-flex items-center">
                    <i class="fas fa-check-double text-emerald-600 mr-1"></i> Terpenuhi [<?php echo e($requests->where('status', 'fulfilled')->count()); ?>]
                </span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
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
                        <?php echo $__env->make('pum.requests.columns.tanggal', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.jumlah', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.keterangan', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.progress', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.status', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pum.requests.columns.aksi', ['request' => $request], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Tidak ada data permintaan.</p>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Pemrograman\magang\pum\resources\views/pum/requests/myrequest.blade.php ENDPATH**/ ?>