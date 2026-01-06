<?php $__env->startSection('title', 'Kelola Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full mx-auto" x-data="{
    ...tableFilter({
        search: '<?php echo e(request('search')); ?>',
        dateFrom: '<?php echo e(request('date_from')); ?>',
        dateTo: '<?php echo e(request('date_to')); ?>'
    })
}">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Kelola Users</h2>
                <a href="<?php echo e(route('users.create')); ?>" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Tambah User
                </a>
            </div>
        </div>

        <!-- Table Filter Component -->
        <?php if (isset($component)) { $__componentOriginala89b5f7816c98ef859487b3b3130b556 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala89b5f7816c98ef859487b3b3130b556 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-filter','data' => ['searchPlaceholder' => 'Cari nama, username, atau email...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['search-placeholder' => 'Cari nama, username, atau email...']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala89b5f7816c98ef859487b3b3130b556)): ?>
<?php $attributes = $__attributesOriginala89b5f7816c98ef859487b3b3130b556; ?>
<?php unset($__attributesOriginala89b5f7816c98ef859487b3b3130b556); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala89b5f7816c98ef859487b3b3130b556)): ?>
<?php $component = $__componentOriginala89b5f7816c98ef859487b3b3130b556; ?>
<?php unset($__componentOriginala89b5f7816c98ef859487b3b3130b556); ?>
<?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Username
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Dibuat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($user->username); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($user->email); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($user->role): ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php if($user->role->name === 'admin'): ?> bg-red-100 text-red-800
                                    <?php elseif($user->role->name === 'librarian'): ?> bg-blue-100 text-blue-800
                                    <?php else: ?> bg-green-100 text-green-800 <?php endif; ?>">
                                    <?php echo e($user->role->display_name); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-gray-500">Tidak ada role</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($user->created_at->format('d/m/Y H:i')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo e(route('users.edit', $user)); ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <?php if($user->id !== auth()->id()): ?>
                                <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        Hapus
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-400">(Akun Anda)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($users->hasPages()): ?>
        <div class="px-6 py-3 border-t border-gray-200">
            <?php echo e($users->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/users/index.blade.php ENDPATH**/ ?>