<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-1">Selamat Datang, <?php echo e($user->name); ?>!</h2>
            <p class="text-gray-600"><?php echo e($user->role->display_name ?? 'User'); ?> - Sistem Permintaan Uang Muka</p>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <?php if(isset($pumStats) || isset($userCount) || isset($roleCount) || isset($permissionCount) || isset($orgTypeCount) || isset($orgUnitCount)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php if(isset($pumStats)): ?>
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['value' => $pumStats['total'],'label' => 'Total Permintaan','icon' => 'fas fa-file-invoice-dollar','color' => 'blue','href' => route('pum-requests.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pumStats['total']),'label' => 'Total Permintaan','icon' => 'fas fa-file-invoice-dollar','color' => 'blue','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('pum-requests.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['value' => $pumStats['pending'],'label' => 'Menunggu','icon' => 'fas fa-clock','color' => 'yellow','href' => route('pum-requests.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pumStats['pending']),'label' => 'Menunggu','icon' => 'fas fa-clock','color' => 'yellow','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('pum-requests.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['value' => $pumStats['approved'],'label' => 'Disetujui','icon' => 'fas fa-check-circle','color' => 'green','href' => route('pum-requests.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pumStats['approved']),'label' => 'Disetujui','icon' => 'fas fa-check-circle','color' => 'green','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('pum-requests.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <?php endif; ?>
        <?php if(isset($userCount)): ?>
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['value' => $userCount,'label' => 'Pengguna','icon' => 'fas fa-users','color' => 'indigo','href' => route('users.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($userCount),'label' => 'Pengguna','icon' => 'fas fa-users','color' => 'indigo','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('users.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <?php endif; ?>
        <?php if(isset($roleCount)): ?>
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['value' => $roleCount,'label' => 'Role','icon' => 'fas fa-user-tag','color' => 'purple','href' => route('roles.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($roleCount),'label' => 'Role','icon' => 'fas fa-user-tag','color' => 'purple','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('roles.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <?php endif; ?>
        <?php if(isset($orgUnitCount)): ?>
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['value' => $orgUnitCount,'label' => 'Unit Organisasi','icon' => 'fas fa-building','color' => 'teal','href' => route('organization-units.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orgUnitCount),'label' => 'Unit Organisasi','icon' => 'fas fa-building','color' => 'teal','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('organization-units.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php
        $hasApprovals = isset($pendingApprovals) || $user->hasPermission('approve_pum');
        $hasRequests = isset($recentRequests) || $user->hasPermission('manage_pum') || $user->hasPermission('create_pum');
        $showTwoColumns = $hasApprovals && $hasRequests;
    ?>

    <div class="grid grid-cols-1 <?php echo e($showTwoColumns ? 'lg:grid-cols-2' : ''); ?> gap-6">
        <!-- Pending Approvals Widget -->
        <?php if(isset($pendingApprovals) && $pendingApprovals->count() > 0): ?>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 bg-orange-50 border-b border-orange-100 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-bell text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-orange-900">Menunggu Persetujuan Anda</h3>
                        <p class="text-xs text-orange-600"><?php echo e($pendingApprovalsCount); ?> permintaan</p>
                    </div>
                </div>
                <a href="<?php echo e(route('pum-approvals.index')); ?>" class="text-sm text-orange-600 hover:text-orange-800">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="<?php echo e(route('pum-requests.show', $approval)); ?>" class="font-medium text-indigo-600 hover:text-indigo-800">
                                <?php echo e($approval->code); ?>

                            </a>
                            <p class="text-sm text-gray-500 truncate"><?php echo e($approval->requester->name ?? '-'); ?></p>
                        </div>
                        <div class="text-right mx-4">
                            <p class="font-bold text-green-600">Rp <?php echo e(number_format($approval->amount, 0, ',', '.')); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($approval->request_date->format('d/m/Y')); ?></p>
                        </div>
                        <div class="flex gap-1">
                            <a href="<?php echo e(route('pum-requests.show', $approval)); ?>" class="p-2 bg-green-100 text-green-700 rounded hover:bg-green-200" title="Lihat & Setujui">
                                <i class="fas fa-check"></i>
                            </a>
                            <a href="<?php echo e(route('pum-requests.show', $approval)); ?>" class="p-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php elseif($user->hasPermission('approve_pum')): ?>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">Tidak Ada Approval Pending</h3>
                <p class="text-sm text-gray-500">Semua permintaan sudah diproses</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- My Recent Requests Widget -->
        <?php if(isset($recentRequests) && $recentRequests->count() > 0): ?>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 bg-blue-50 border-b border-blue-100 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-file-alt text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-blue-900">Permintaan Terbaru</h3>
                        <p class="text-xs text-blue-600">5 permintaan terakhir</p>
                    </div>
                </div>
                <a href="<?php echo e(route('pum-requests.index')); ?>" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="<?php echo e(route('pum-requests.show', $request)); ?>" class="font-medium text-indigo-600 hover:text-indigo-800">
                                <?php echo e($request->code); ?>

                            </a>
                            <p class="text-sm text-gray-500 truncate"><?php echo e(Str::limit($request->description, 30) ?? '-'); ?></p>
                        </div>
                        <div class="text-right mx-4">
                            <p class="font-bold text-gray-900">Rp <?php echo e(number_format($request->amount, 0, ',', '.')); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($request->created_at->format('d/m/Y')); ?></p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $request->status,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($request->status),'size' => 'sm']); ?>
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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="p-4 bg-gray-50 border-t">
                <a href="<?php echo e(route('pum-requests.create')); ?>" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Buat Permintaan Baru
                </a>
            </div>
        </div>
        <?php elseif($user->hasPermission('manage_pum') || $user->hasPermission('create_pum')): ?>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-invoice-dollar text-blue-500 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">Belum Ada Permintaan</h3>
                <p class="text-sm text-gray-500 mb-4">Buat permintaan uang muka pertama Anda</p>
                <a href="<?php echo e(route('pum-requests.create')); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Buat Permintaan
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions Card -->
    <?php
        $quickActions = collect([
            ['permission' => 'manage_pum|create_pum', 'href' => route('pum-requests.create'), 'icon' => 'fas fa-plus', 'title' => 'Buat Permintaan', 'subtitle' => 'Ajukan uang muka', 'color' => 'green'],
            ['permission' => 'approve_pum', 'href' => route('pum-approvals.index'), 'icon' => 'fas fa-clipboard-check', 'title' => 'Approval', 'subtitle' => ($pendingApprovalsCount ?? 0) . ' menunggu', 'color' => 'orange'],
            ['permission' => 'manage_users', 'href' => route('users.index'), 'icon' => 'fas fa-users', 'title' => 'Kelola Pengguna', 'subtitle' => ($userCount ?? 0) . ' pengguna', 'color' => 'blue'],
            ['permission' => 'manage_roles', 'href' => route('roles.index'), 'icon' => 'fas fa-user-tag', 'title' => 'Kelola Role', 'subtitle' => ($roleCount ?? 0) . ' role', 'color' => 'purple'],
            ['permission' => 'manage_organization_units', 'href' => route('organization-units.index'), 'icon' => 'fas fa-building', 'title' => 'Unit Organisasi', 'subtitle' => ($orgUnitCount ?? 0) . ' unit', 'color' => 'teal'],
            ['permission' => 'manage_pum_workflows', 'href' => route('pum-workflows.index'), 'icon' => 'fas fa-project-diagram', 'title' => 'Workflow PUM', 'subtitle' => 'Kelola approval', 'color' => 'indigo'],
        ])->filter(function($action) use ($user) {
            // Handle multiple permissions separated by |
            $permissions = explode('|', $action['permission']);
            foreach ($permissions as $perm) {
                if ($user->hasPermission(trim($perm))) {
                    return true;
                }
            }
            return false;
        });
        
        $actionGridClass = match($quickActions->count()) {
            1 => 'grid-cols-1',
            2 => 'grid-cols-1 md:grid-cols-2',
            3 => 'grid-cols-1 md:grid-cols-3',
            default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        };
    ?>
    
    <?php if($quickActions->count() > 0): ?>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="grid <?php echo e($actionGridClass); ?> gap-4">
                <?php $__currentLoopData = $quickActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginala8d780f63e732384c5d89ba5ec71ad14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['href' => $action['href'],'icon' => $action['icon'],'title' => $action['title'],'subtitle' => $action['subtitle'],'color' => $action['color']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action['href']),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action['icon']),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action['title']),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action['subtitle']),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action['color'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $attributes = $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $component = $__componentOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Pemrograman\magang\pum\resources\views/dashboard.blade.php ENDPATH**/ ?>