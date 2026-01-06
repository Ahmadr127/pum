<?php $__env->startSection('title', 'Detail Workflow - ' . $pumWorkflow->name); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-4xl mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900"><?php echo e($pumWorkflow->name); ?></h2>
                    <p class="text-sm text-gray-500 mt-1"><?php echo e($pumWorkflow->description ?? 'Tidak ada deskripsi'); ?></p>
                </div>
                <div class="flex items-center gap-2">
                    <?php if($pumWorkflow->is_default): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-star mr-1"></i> Default
                        </span>
                    <?php endif; ?>
                    <?php if($pumWorkflow->is_active): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> Aktif
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-times-circle mr-1"></i> Nonaktif
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Workflow Steps -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Langkah-langkah Approval</h3>
                
                <div class="relative">
                    <!-- Connecting Line -->
                    <div class="absolute left-6 top-8 bottom-8 w-0.5 bg-gray-200"></div>
                    
                    <div class="space-y-6">
                        <?php $__currentLoopData = $pumWorkflow->steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="relative flex items-start">
                            <!-- Step Number Circle -->
                            <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-full <?php echo e($step->is_required ? 'bg-indigo-600' : 'bg-gray-400'); ?> text-white font-bold text-lg z-10">
                                <?php echo e($step->order); ?>

                            </div>
                            
                            <!-- Step Content -->
                            <div class="ml-4 flex-1 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900"><?php echo e($step->name); ?></h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-user-check mr-1"></i>
                                            <?php echo e($step->approver_description); ?>

                                        </p>
                                    </div>
                                    <div>
                                        <?php if($step->is_required): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Wajib
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                Opsional
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-3 border-t border-gray-200 pt-6">
                <a href="<?php echo e(route('pum-workflows.index')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="<?php echo e(route('pum-workflows.edit', $pumWorkflow)); ?>" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 font-medium">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <?php if(!$pumWorkflow->is_default): ?>
                    <form action="<?php echo e(route('pum-workflows.set-default', $pumWorkflow)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">
                            <i class="fas fa-star mr-1"></i> Jadikan Default
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/pum/workflows/show.blade.php ENDPATH**/ ?>