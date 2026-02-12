<!-- Modal Backdrop -->
<div x-show="open" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="open = false"
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                         :class="{
                            'bg-green-100 text-green-600': modalStep.status === 'approved',
                            'bg-red-100 text-red-600': modalStep.status === 'rejected',
                            'bg-blue-100 text-blue-600': modalStep.status === 'pending' && modalStep.isCurrent,
                            'bg-gray-100 text-gray-400': !['approved', 'rejected'].includes(modalStep.status) && !modalStep.isCurrent
                         }">
                        <!-- Icon Logic using x-show since icon name is not in JS anymore (unless we put it back, but we can infer) -->
                         <i class="fas" 
                            :class="{
                                'fa-check': modalStep.status === 'approved',
                                'fa-times': modalStep.status === 'rejected',
                                'fa-hourglass-half': modalStep.status === 'pending',
                                'fa-circle': !['approved', 'rejected', 'pending'].includes(modalStep.status)
                            }"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Detail Tahap: <span x-text="modalStep.name" class="text-indigo-600 font-bold"></span>
                        </h3>
                        <div class="mt-4">
                            <div class="space-y-3">
                                <!-- Status -->
                                <div class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-sm font-medium text-gray-500">Status</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                          :class="{
                                            'bg-green-100 text-green-800': modalStep.status === 'approved',
                                            'bg-red-100 text-red-800': modalStep.status === 'rejected',
                                            'bg-blue-100 text-blue-800': modalStep.status === 'pending' && modalStep.isCurrent,
                                            'bg-gray-100 text-gray-800': !['approved', 'rejected'].includes(modalStep.status) && !modalStep.isCurrent
                                          }" 
                                          x-text="modalStep.statusLabel"></span>
                                </div>
                                
                                <!-- Approver Name (Only if actioned or pending) -->
                                <div class="flex justify-between border-b border-gray-100 pb-2" x-show="['approved', 'rejected'].includes(modalStep.status)">
                                    <span class="text-sm font-medium text-gray-500" x-text="modalStep.status === 'rejected' ? 'Ditolak Oleh' : 'Disetujui Oleh'"></span>
                                    <span class="text-sm text-gray-900 font-medium" x-text="modalStep.approverName"></span>
                                </div>

                                <!-- Approver Position / Jabatan (NEW) -->
                                <div class="flex justify-between border-b border-gray-100 pb-2" x-show="['approved', 'rejected'].includes(modalStep.status)">
                                    <span class="text-sm font-medium text-gray-500">Jabatan</span>
                                    <div class="text-right">
                                         <span class="text-sm text-gray-900" x-text="modalStep.approverPosition"></span>
                                    </div>
                                </div>

                                <!-- Date (Only if actioned) -->
                                <div class="flex justify-between border-b border-gray-100 pb-2" x-show="['approved', 'rejected'].includes(modalStep.status)">
                                    <span class="text-sm font-medium text-gray-500">Tanggal</span>
                                    <span class="text-sm text-gray-900" x-text="modalStep.respondedAt"></span>
                                </div>

                                <!-- Notes (Only if has note) -->
                                <div x-show="modalStep.note">
                                    <span class="block text-sm font-medium text-gray-500 mb-1">Catatan:</span>
                                    <div class="bg-gray-50 rounded p-2 text-sm text-gray-700 italic border border-gray-200">
                                        "<span x-text="modalStep.note"></span>"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="open = false">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
