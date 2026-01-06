<?php $__env->startSection('title', 'Edit Workflow - ' . $pumWorkflow->name); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4" x-data="workflowEditForm()">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Edit Workflow Approval</h2>

            <form action="<?php echo e(route('pum-workflows.update', $pumWorkflow)); ?>" method="POST" @submit="prepareSubmit">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <!-- Basic Info -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Workflow <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name', $pumWorkflow->name)); ?>" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <?php $__errorArgs = ['name'];
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

                    <div class="flex items-center gap-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" <?php echo e($pumWorkflow->is_active ? 'checked' : ''); ?>

                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_default" value="1" <?php echo e($pumWorkflow->is_default ? 'checked' : ''); ?>

                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Jadikan Default</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="2"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"><?php echo e(old('description', $pumWorkflow->description)); ?></textarea>
                </div>

                <!-- Steps Section -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Langkah-langkah Approval</h3>
                        <button type="button" @click="addStep" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-md text-sm hover:bg-indigo-200">
                            <i class="fas fa-plus mr-1"></i> Tambah Step
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(step, index) in steps" :key="index">
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 relative">
                                <!-- Step Number -->
                                <div class="absolute -top-3 left-4 bg-indigo-600 text-white px-2 py-0.5 rounded text-xs font-medium">
                                    Step <span x-text="index + 1"></span>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" @click="removeStep(index)" 
                                        x-show="steps.length > 1"
                                        class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                                    <i class="fas fa-times"></i>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                    <!-- Step Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Step *</label>
                                        <input type="text" x-model="step.name" required
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    </div>

                                    <!-- Approver Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Approver *</label>
                                        <select x-model="step.approver_type" @change="onApproverTypeChange(index)"
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            <option value="role">Berdasarkan Role</option>
                                            <option value="user">User Spesifik</option>
                                            <option value="organization_head">Kepala Unit Organisasi</option>
                                        </select>
                                    </div>

                                    <!-- Role Selector -->
                                    <div x-show="step.approver_type === 'role'">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Role *</label>
                                        <select x-model="step.role_id"
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            <option value="">Pilih Role...</option>
                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($role->id); ?>"><?php echo e($role->display_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <!-- User Selector -->
                                    <div x-show="step.approver_type === 'user'">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih User *</label>
                                        <select x-model="step.user_id"
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            <option value="">Pilih User...</option>
                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div x-show="step.approver_type === 'organization_head'" class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Kepala unit organisasi pemohon
                                    </div>
                                </div>

                                <!-- Required Checkbox -->
                                <div class="mt-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="step.is_required"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Wajib (Required)</span>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Hidden inputs for steps -->
                    <template x-for="(step, index) in steps" :key="'input_' + index">
                        <div>
                            <input type="hidden" :name="'steps[' + index + '][id]'" :value="step.id || ''">
                            <input type="hidden" :name="'steps[' + index + '][name]'" :value="step.name">
                            <input type="hidden" :name="'steps[' + index + '][approver_type]'" :value="step.approver_type">
                            <input type="hidden" :name="'steps[' + index + '][role_id]'" :value="step.role_id || ''">
                            <input type="hidden" :name="'steps[' + index + '][user_id]'" :value="step.user_id || ''">
                            <input type="hidden" :name="'steps[' + index + '][is_required]'" :value="step.is_required ? '1' : '0'">
                        </div>
                    </template>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
                    <a href="<?php echo e(route('pum-workflows.show', $pumWorkflow)); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function workflowEditForm() {
    return {
        steps: <?php echo json_encode($stepsData, 15, 512) ?>,

        addStep() {
            this.steps.push({ 
                id: null,
                name: '', 
                approver_type: 'role', 
                role_id: '', 
                user_id: '', 
                is_required: true 
            });
        },

        removeStep(index) {
            if (this.steps.length > 1) {
                this.steps.splice(index, 1);
            }
        },

        onApproverTypeChange(index) {
            this.steps[index].role_id = '';
            this.steps[index].user_id = '';
        },

        prepareSubmit(e) {
            for (let step of this.steps) {
                if (!step.name) {
                    alert('Semua step harus memiliki nama');
                    e.preventDefault();
                    return false;
                }
                if (step.approver_type === 'role' && !step.role_id) {
                    alert('Pilih role untuk setiap step dengan tipe "Berdasarkan Role"');
                    e.preventDefault();
                    return false;
                }
                if (step.approver_type === 'user' && !step.user_id) {
                    alert('Pilih user untuk setiap step dengan tipe "User Spesifik"');
                    e.preventDefault();
                    return false;
                }
            }
            return true;
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/data/Education/Pemrograman/magang/pum/pum/resources/views/pum/workflows/edit.blade.php ENDPATH**/ ?>