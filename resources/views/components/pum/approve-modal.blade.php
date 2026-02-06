@props([
    'action' => '',
    'pumRequest' => null,
    'modalName' => 'approve-modal',
    'showDetails' => false
])

<div 
    x-data="{ 
        open: false, 
        requestId: null, 
        code: '', 
        amount: 0,
        formAction: '{{ $action }}'
    }"
    x-on:open-modal.window="if ($event.detail === '{{ $modalName }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $modalName }}') open = false"
    @if($showDetails)
    x-on:open-quick-approve.window="
        open = true;
        requestId = $event.detail.id;
        code = $event.detail.code;
        amount = $event.detail.amount;
        formAction = `/pum-requests/${$event.detail.id}/approve`;
    "
    @endif
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    role="dialog"
    aria-modal="true"
>
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="open = false"
    ></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-md"
                @click.away="open = false"
            >
                <form :action="formAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Setujui Permintaan</h3>
                                @if($showDetails)
                                <p class="text-sm text-gray-500 mt-1" x-text="code"></p>
                                <p class="text-lg font-bold text-green-600 mt-2" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(amount)"></p>
                                @endif
                                <div class="mt-4">
                                    <label for="approve_notes_{{ $modalName }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Catatan (Opsional)
                                    </label>
                                    <textarea 
                                        name="notes" 
                                        id="approve_notes_{{ $modalName }}" 
                                        rows="3"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                        placeholder="Tambahkan catatan persetujuan..."
                                    ></textarea>
                                </div>

                                <!-- FS Form Upload (Optional) -->
                                <div class="mt-4">
                                    <label for="fs_form_{{ $modalName }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Form FS (Opsional)
                                        <span class="text-xs text-gray-500 font-normal">- PDF, DOC, DOCX (Max 5MB)</span>
                                    </label>
                                    <input 
                                        type="file" 
                                        name="fs_form" 
                                        id="fs_form_{{ $modalName }}"
                                        accept=".pdf,.doc,.docx"
                                        class="block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-md file:border-0
                                            file:text-sm file:font-medium
                                            file:bg-green-50 file:text-green-700
                                            hover:file:bg-green-100
                                            cursor-pointer"
                                    >
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Upload form FS jika diperlukan untuk approval ini
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button 
                            type="submit"
                            class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            <i class="fas fa-check mr-2"></i> Setujui
                        </button>
                        <button 
                            type="button"
                            @click="open = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

