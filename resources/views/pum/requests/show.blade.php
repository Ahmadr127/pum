@extends('layouts.app')

@section('title', 'Detail Permintaan - ' . $pumRequest->code)

@section('content')
<div class="w-full">
    <div class="max-w-full px-6">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3" role="alert">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <span class="block sm:inline font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3" role="alert">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                <span class="block sm:inline font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column: Main Content -->
            <div class="flex-1 min-w-0 space-y-6">
                <!-- Detail Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Permintaan</h1>
                                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                                    <i class="fas fa-hashtag text-gray-300"></i>
                                    <span class="font-mono">{{ $pumRequest->code }}</span>
                                    <span class="text-gray-300">|</span>
                                    <i class="fas fa-calendar-alt text-gray-300"></i>
                                    <span>{{ $pumRequest->request_date->format('d M Y') }}</span>
                                </div>
                            </div>
                            {{-- Print button --}}
                            <a href="{{ route('pum-requests.print', $pumRequest) }}" target="_blank"
                               class="inline-flex items-center gap-2 bg-gray-700 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                                <i class="fas fa-print"></i>
                                Print
                            </a>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pengaju</label>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ substr($pumRequest->requester->name ?? 'User', 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $pumRequest->requester->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $pumRequest->requester->organizationUnit->name ?? 'Unit tidak diketahui' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Nominal</label>
                                <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($pumRequest->amount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Keterangan / Keperluan</label>
                            <div class="prose prose-sm max-w-none bg-white text-gray-700 p-0">
                                @if($pumRequest->description)
                                    <p class="whitespace-pre-line leading-relaxed text-gray-600">{{ $pumRequest->description }}</p>
                                @else
                                    <p class="italic text-gray-400">Tidak ada keterangan lampiran.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="mb-6 border-t border-gray-100 pt-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Dokumen Lampiran</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Lampiran Utama -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-2 uppercase">Lampiran Utama</label>
                                    @if($pumRequest->attachments && count($pumRequest->attachments) > 0)
                                        <div class="space-y-2">
                                            @foreach($pumRequest->attachments as $attachment)
                                            <a href="{{ Storage::url($attachment) }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                                <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-indigo-500 shadow-sm">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <span class="text-sm text-gray-700 group-hover:text-indigo-700 truncate flex-1 font-medium">{{ basename($attachment) }}</span>
                                                <i class="fas fa-external-link-alt text-gray-300 group-hover:text-indigo-400 text-xs"></i>
                                            </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400 italic">Tidak ada lampiran utama.</p>
                                    @endif
                                </div>

                                <!-- Lampiran Tambahan -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-2 uppercase">Lampiran Tambahan</label>
                                    @if($pumRequest->attachments2 && count($pumRequest->attachments2) > 0)
                                        <div class="space-y-2">
                                            @foreach($pumRequest->attachments2 as $attachment)
                                            <a href="{{ Storage::url($attachment) }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                                <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-indigo-500 shadow-sm">
                                                    <i class="fas fa-paperclip"></i>
                                                </div>
                                                <span class="text-sm text-gray-700 group-hover:text-indigo-700 truncate flex-1 font-medium">{{ basename($attachment) }}</span>
                                                <i class="fas fa-external-link-alt text-gray-300 group-hover:text-indigo-400 text-xs"></i>
                                            </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400 italic">Tidak ada lampiran tambahan.</p>
                                    @endif
                                </div>

                                </div>
                            </div>
                            
                            <!-- Dokumen FS -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <label class="block text-xs font-medium text-gray-500 mb-2 uppercase">Dokumen FS</label>
                                @php
                                    // Collect all approvals with FS forms
                                    $fsApprovals = $pumRequest->approvals->whereNotNull('fs_form_path');
                                @endphp
                                
                                @if($fsApprovals->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($fsApprovals as $approval)
                                        <a href="{{ Storage::url($approval->fs_form_path) }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                            <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-indigo-500 shadow-sm shrink-0">
                                                <i class="fas fa-file-contract"></i>
                                            </div>
                                            <span class="text-sm text-gray-700 group-hover:text-indigo-700 truncate flex-1 font-medium" title="{{ basename($approval->fs_form_path) }}">{{ basename($approval->fs_form_path) }}</span>
                                            <i class="fas fa-external-link-alt text-gray-300 group-hover:text-indigo-400 text-xs"></i>
                                        </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-sm text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <i class="fas fa-info-circle text-gray-300"></i>
                                        Tidak ada dokumen FS yang diunggah.
                                    </div>
                                @endif
                            </div>


                        <!-- Metadata Footer -->
                        <div class="border-t border-gray-100 pt-4 flex flex-wrap gap-4 text-xs text-gray-400">
                            <span>Dibuat oleh: <span class="text-gray-600">{{ $pumRequest->creator->name ?? '-' }}</span></span>
                            <span>&bull;</span>
                            <span>Waktu: {{ $pumRequest->created_at->format('d/m/Y H:i') }}</span>
                            @if($pumRequest->workflow)
                                <span>&bull;</span>
                                <span>Workflow: <span class="text-gray-600">{{ $pumRequest->workflow->name }}</span></span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Approval History -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Riwayat Persetujuan</h3>
                        <span class="text-xs bg-white border border-gray-200 px-2 py-1 rounded text-gray-500">
                            {{ $pumRequest->approvals->count() }} Tahap
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahap</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approver</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($pumRequest->approvals as $approval)
                                <tr class="{{ $approval->status === 'pending' && $pumRequest->current_step_order === $approval->step_order ? 'bg-indigo-50/50' : 'hover:bg-gray-50/50' }} transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium">{{ $approval->step->name ?? '-' }}</span>
                                                @if($approval->fs_form_path)
                                                    <a href="{{ Storage::url($approval->fs_form_path) }}" target="_blank" 
                                                       class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs hover:bg-blue-100 transition-colors" 
                                                       title="Download FS Form">
                                                        <i class="fas fa-file-contract"></i>
                                                        <span class="max-w-[150px] truncate">{{ basename($approval->fs_form_path) }}</span>
                                                        <i class="fas fa-download text-[10px]"></i>
                                                    </a>
                                                @endif
                                            </div>
                                            
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $approval->approver->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($approval->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1.5"></i> Disetujui
                                            </span>
                                        @elseif($approval->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1.5"></i> Ditolak
                                            </span>
                                        @elseif($approval->status === 'pending')
                                            @if($pumRequest->current_step_order === $approval->step_order)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 animate-pulse">
                                                    <i class="fas fa-clock mr-1.5"></i> Menunggu
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-hourglass-start mr-1.5"></i> Mengantri
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $approval->responded_at ? $approval->responded_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $approval->notes ?? '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                                        Belum ada data approval.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="w-full lg:w-[35%] space-y-6">
                <!-- Status & Actions Card -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-0">Status Pengajuan</h3>
                        <x-status-badge :status="$pumRequest->status" size="md" />
                    </div>

                    <!-- Actions -->
                    <div class="space-y-4">
                        @if($pumRequest->status === 'new')
                            <a href="{{ route('pum-requests.edit', $pumRequest) }}" class="block w-full text-center px-4 py-2 bg-yellow-400 text-yellow-900 rounded-lg hover:bg-yellow-500 transition-colors font-medium text-sm shadow-sm group">
                                <i class="fas fa-edit mr-2 group-hover:scale-110 transition-transform"></i> Edit Data
                            </a>
                            
                            <form action="{{ route('pum-requests.submit', $pumRequest) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full justify-center flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm shadow-md group">
                                    <i class="fas fa-paper-plane mr-2 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 transition-transform"></i> Ajukan Sekarang
                                </button>
                            </form>
                        @endif

                        @if($canApprove)
                            <div class="border-t border-gray-100 pt-4 mt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Form Persetujuan</h4>
                                
                                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    
                                    <!-- Notes -->
                                    <div>
                                        <label for="notes" class="block text-xs font-medium text-gray-500 mb-1">Catatan (Opsional saat Menyetujui, Wajib saat Menolak)</label>
                                        <textarea name="notes" id="notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm resize-none placeholder-gray-400" placeholder="Tulis catatan persetujuan/penolakan..."></textarea>
                                        @error('notes')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- FS Form Upload (Conditional) -->
                                    @php
                                        $currentStep = $pumRequest->getCurrentStep();
                                    @endphp
                                    
                                    @if($currentStep && $currentStep->is_upload_fs_required)
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Upload Dokumen FS <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="file" name="fs_form" id="fs_form" required class="block w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-1">Wajib upload form FS untuk tahap ini.</p>
                                            @error('fs_form')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="grid grid-cols-2 gap-3 pt-2">
                                        <button type="submit" formaction="{{ route('pum-requests.reject', $pumRequest) }}" class="flex items-center justify-center px-3 py-2 bg-white border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors font-medium text-sm shadow-sm">
                                            <i class="fas fa-times mr-2"></i> Tolak
                                        </button>
                                        <button type="submit" formaction="{{ route('pum-requests.approve', $pumRequest) }}" class="flex items-center justify-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm shadow-sm">
                                            <i class="fas fa-check mr-2"></i> Setujui
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                        
                        @if($pumRequest->status === 'new' || $pumRequest->status === 'draft')
                             <!-- Optional: Delete button if needed -->
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

