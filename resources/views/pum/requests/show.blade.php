@extends('layouts.app')

@section('title', 'Detail Permintaan - ' . $pumRequest->code)

@section('content')
<div class="w-full">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">DETAIL PERMINTAAN UANG MUKA</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $pumRequest->code }}</p>
                </div>
                <x-status-badge :status="$pumRequest->status" size="lg" />
            </div>

            <!-- Request Details -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">PENGAJU</label>
                        <p class="text-gray-900 font-medium text-lg">{{ $pumRequest->requester->name ?? '-' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">TANGGAL</label>
                            <p class="text-gray-900">{{ $pumRequest->request_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">DIBUAT OLEH</label>
                            <p class="text-gray-900">{{ $pumRequest->creator->name ?? '-' }}</p>
                            <p class="text-gray-500 text-xs">{{ $pumRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">JUMLAH DIAJUKAN</label>
                        <p class="text-gray-900 font-bold text-2xl text-green-600">Rp {{ number_format($pumRequest->amount, 0, ',', '.') }}</p>
                    </div>

                    @if($pumRequest->workflow)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">WORKFLOW APPROVAL</label>
                        <p class="text-gray-900">{{ $pumRequest->workflow->name }}</p>
                    </div>
                    @endif
                </div>

                <!-- Middle Column - Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">KETERANGAN</label>
                    <div class="bg-gray-50 p-4 rounded-md min-h-32 text-gray-900">
                        {{ $pumRequest->description ?? 'Tidak ada keterangan' }}
                    </div>
                </div>

                <!-- Third Column - Lampiran 1 -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">LAMPIRAN 1</label>
                    @if($pumRequest->attachments && count($pumRequest->attachments) > 0)
                    <div class="space-y-2">
                        @foreach($pumRequest->attachments as $attachment)
                        <a href="{{ Storage::url($attachment) }}" target="_blank" 
                           class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            @php
                                $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                $iconClass = match(strtolower($ext)) {
                                    'pdf' => 'fa-file-pdf text-red-500',
                                    'doc', 'docx' => 'fa-file-word text-blue-500',
                                    'xls', 'xlsx' => 'fa-file-excel text-green-500',
                                    'jpg', 'jpeg', 'png' => 'fa-file-image text-purple-500',
                                    default => 'fa-file text-gray-500'
                                };
                            @endphp
                            <i class="fas {{ $iconClass }} text-lg"></i>
                            <span class="text-sm text-gray-700 flex-1 truncate">{{ basename($attachment) }}</span>
                            <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-gray-50 p-4 rounded-md text-center text-gray-400">
                        <i class="fas fa-paperclip text-2xl mb-2"></i>
                        <p class="text-sm">Tidak ada lampiran</p>
                    </div>
                    @endif
                </div>

                <!-- Fourth Column - Lampiran 2 -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">LAMPIRAN 2</label>
                    @if($pumRequest->attachments2 && count($pumRequest->attachments2) > 0)
                    <div class="space-y-2">
                        @foreach($pumRequest->attachments2 as $attachment)
                        <a href="{{ Storage::url($attachment) }}" target="_blank" 
                           class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            @php
                                $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                $iconClass = match(strtolower($ext)) {
                                    'pdf' => 'fa-file-pdf text-red-500',
                                    'doc', 'docx' => 'fa-file-word text-blue-500',
                                    'xls', 'xlsx' => 'fa-file-excel text-green-500',
                                    'jpg', 'jpeg', 'png' => 'fa-file-image text-purple-500',
                                    default => 'fa-file text-gray-500'
                                };
                            @endphp
                            <i class="fas {{ $iconClass }} text-lg"></i>
                            <span class="text-sm text-gray-700 flex-1 truncate">{{ basename($attachment) }}</span>
                            <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-gray-50 p-4 rounded-md text-center text-gray-400">
                        <i class="fas fa-paperclip text-2xl mb-2"></i>
                        <p class="text-sm">Tidak ada lampiran</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-200 pt-6">
                @if($pumRequest->status === 'new')
                    <a href="{{ route('pum-requests.edit', $pumRequest) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 font-medium">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    
                    <form action="{{ route('pum-requests.submit', $pumRequest) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                            <i class="fas fa-paper-plane mr-1"></i> Ajukan Persetujuan
                        </button>
                    </form>
                @endif

                @if($canApprove)
                    <button 
                        type="button" 
                        @click="$dispatch('open-modal', 'approve-modal')" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium"
                    >
                        <i class="fas fa-check mr-1"></i> Setujui
                    </button>
                    
                    <button 
                        type="button" 
                        @click="$dispatch('open-modal', 'reject-modal')" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium"
                    >
                        <i class="fas fa-times mr-1"></i> Tolak
                    </button>
                @endif


            </div>
        </div>

        {{-- Riwayat Persetujuan - Hanya untuk Pengaju --}}
        @if(auth()->id() == $pumRequest->requester_id || auth()->id() == $pumRequest->created_by)
        <div class="p-6 bg-gray-50">
            <h3 class="text-sm font-medium text-blue-600 mb-4">Riwayat Persetujuan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Step</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Form FS</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disetujui Oleh</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pumRequest->approvals as $approval)
                        <tr class="{{ $approval->status === 'pending' && $pumRequest->current_step_order === $approval->step_order ? 'bg-yellow-50' : '' }}">
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                {{ $approval->responded_at ? $approval->responded_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span class="font-medium">{{ $approval->step->name ?? '-' }}</span>
                                @if($pumRequest->current_step_order === $approval->step_order && $approval->status === 'pending')
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-arrow-right mr-1"></i> Tahap Ini
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">
                                {{ $approval->notes ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                @if($approval->fs_form_path)
                                    <a href="{{ Storage::url($approval->fs_form_path) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline">
                                        <i class="fas fa-file-pdf text-red-500"></i>
                                        <span>Lihat FS</span>
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                {{ $approval->approver->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($approval->status === 'approved')
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </span>
                                @elseif($approval->status === 'rejected')
                                    <span class="inline-flex items-center text-red-600">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-gray-400">
                                        <i class="fas fa-clock text-lg"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-400 text-sm">
                                Belum ada riwayat persetujuan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Approve Modal Component --}}
@if($canApprove)
<x-pum.approve-modal :action="route('pum-requests.approve', $pumRequest)" />
<x-pum.reject-modal :action="route('pum-requests.reject', $pumRequest)" />
@endif

@endsection

