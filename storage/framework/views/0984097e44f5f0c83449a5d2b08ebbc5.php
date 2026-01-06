<?php $__env->startSection('title', 'Detail Unit Organisasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded 
                            <?php if($organizationUnit->type->level == 1): ?> bg-purple-100 text-purple-800
                            <?php elseif($organizationUnit->type->level == 2): ?> bg-blue-100 text-blue-800
                            <?php elseif($organizationUnit->type->level == 3): ?> bg-green-100 text-green-800
                            <?php elseif($organizationUnit->type->level == 4): ?> bg-yellow-100 text-yellow-800
                            <?php else: ?> bg-gray-100 text-gray-800
                            <?php endif; ?>">
                            <?php echo e($organizationUnit->type->display_name); ?>

                        </span>
                        <span class="inline-flex px-2 py-1 text-xs font-mono bg-gray-200 text-gray-700 rounded">
                            <?php echo e($organizationUnit->code); ?>

                        </span>
                        <?php if($organizationUnit->is_active): ?>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        <?php else: ?>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900"><?php echo e($organizationUnit->name); ?></h2>
                    <p class="text-sm text-gray-500 mt-1"><?php echo e($organizationUnit->full_path); ?></p>
                    <?php if($organizationUnit->description): ?>
                        <p class="text-gray-600 mt-2"><?php echo e($organizationUnit->description); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('organization-units.edit', $organizationUnit)); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                    <a href="<?php echo e(route('organization-units.index')); ?>" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Info & Kepala Unit -->
        <div class="space-y-6">
            <!-- Parent Unit -->
            <?php if($organizationUnit->parent): ?>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Parent Unit</h3>
                    <a href="<?php echo e(route('organization-units.show', $organizationUnit->parent)); ?>" 
                       class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900"><?php echo e($organizationUnit->parent->name); ?></p>
                            <p class="text-sm text-gray-500"><?php echo e($organizationUnit->parent->type->display_name); ?></p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Kepala Unit -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                        <i class="fas fa-user-tie text-green-600 mr-2"></i>Kepala Unit
                    </h3>
                    
                    <?php if($organizationUnit->head): ?>
                        <div class="flex items-center p-3 bg-green-50 rounded-lg mb-3">
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900"><?php echo e($organizationUnit->head->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($organizationUnit->head->email); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 mb-3">Belum ditentukan</p>
                    <?php endif; ?>

                    <form action="<?php echo e(route('organization-units.update-head', $organizationUnit)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="flex gap-2 items-end">
                            <div class="flex-1">
                                <?php if (isset($component)) { $__componentOriginal0728ba7b37b7eda62f62767c5dccebf3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0728ba7b37b7eda62f62767c5dccebf3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-dropdown','data' => ['name' => 'head_id','options' => $allUsers->map(fn($u) => (object)['id' => $u->id, 'name' => $u->name]),'valueField' => 'id','labelField' => 'name','selected' => $organizationUnit->head_id,'placeholder' => 'Pilih Kepala Unit...','emptyOption' => '-- Tidak Ada --']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'head_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($allUsers->map(fn($u) => (object)['id' => $u->id, 'name' => $u->name])),'value-field' => 'id','label-field' => 'name','selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($organizationUnit->head_id),'placeholder' => 'Pilih Kepala Unit...','empty-option' => '-- Tidak Ada --']); ?>
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
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm h-[38px]">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sub Units -->
            <?php if($organizationUnit->children->count() > 0): ?>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                        <i class="fas fa-sitemap text-blue-600 mr-2"></i>Sub-Unit (<?php echo e($organizationUnit->children->count()); ?>)
                    </h3>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $organizationUnit->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('organization-units.show', $child)); ?>" 
                               class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900"><?php echo e($child->name); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e($child->type->display_name); ?> • <?php echo e($child->code); ?></p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Members -->
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-users text-indigo-600 mr-2"></i>Anggota Unit (<?php echo e($organizationUnit->members->count()); ?>)
                        </h3>
                    </div>

                    <!-- Add Member Form -->
                    <form action="<?php echo e(route('organization-units.add-member', $organizationUnit)); ?>" method="POST" class="mb-4">
                        <?php echo csrf_field(); ?>
                        <div class="flex gap-2 items-end">
                            <div class="flex-1">
                                <?php if (isset($component)) { $__componentOriginal0728ba7b37b7eda62f62767c5dccebf3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0728ba7b37b7eda62f62767c5dccebf3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-dropdown','data' => ['name' => 'user_id','options' => $availableUsers->map(fn($u) => (object)['id' => $u->id, 'name' => $u->name . ' (' . $u->email . ')']),'valueField' => 'id','labelField' => 'name','selected' => null,'placeholder' => 'Pilih User untuk Ditambahkan...','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableUsers->map(fn($u) => (object)['id' => $u->id, 'name' => $u->name . ' (' . $u->email . ')'])),'value-field' => 'id','label-field' => 'name','selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(null),'placeholder' => 'Pilih User untuk Ditambahkan...','required' => true]); ?>
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
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm whitespace-nowrap h-[38px]">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </button>
                        </div>
                    </form>

                    <!-- Members Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $organizationUnit->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo e($member->name); ?></p>
                                                <?php if($organizationUnit->head_id == $member->id): ?>
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-800">Kepala</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($member->email); ?>

                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                            <?php echo e($member->role->display_name ?? '-'); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php if($organizationUnit->head_id == $member->id): ?>
                                            <span class="text-green-600 font-medium">Kepala Unit</span>
                                        <?php else: ?>
                                            <span class="text-gray-500">Anggota</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <?php if($organizationUnit->head_id != $member->id): ?>
                                            <form action="<?php echo e(route('organization-units.remove-member', [$organizationUnit, $member])); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Yakin ingin menghapus <?php echo e($member->name); ?> dari unit ini?')">
                                                    <i class="fas fa-user-minus"></i> Hapus
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                                        <p>Belum ada anggota di unit ini.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Pemrograman\magang\pum\resources\views/organization-units/show.blade.php ENDPATH**/ ?>