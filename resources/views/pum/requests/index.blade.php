@extends('layouts.app')

@section('title', 'Permintaan Uang Muka')

@section('content')
<div class="w-full">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header with Add Button -->
        <div class="px-4 py-3 bg-white border-b border-gray-200 flex justify-end">
            <a href="{{ route('pum-requests.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded inline-flex items-center text-sm">
                <i class="fas fa-plus mr-2"></i>
                Tambah Pengajuan
            </a>
        </div>

        <!-- Filters - Single Row -->
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <form action="{{ route('pum-requests.index') }}" method="GET">
                <div class="flex flex-wrap items-end gap-3">
                    <!-- Dari Tanggal -->
                    <div class="w-36">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="w-36">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>

                    <!-- Nama Pengaju - Searchable -->
                    <div class="w-48">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Pengaju</label>
                        <x-searchable-dropdown 
                            name="requester_id"
                            :options="$users"
                            value-field="id"
                            label-field="name"
                            :selected="request('requester_id')"
                            placeholder="Semua"
                            empty-option="Semua"
                        />
                    </div>

                    <!-- Status - Searchable -->
                    <div class="w-44">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <x-searchable-dropdown 
                            name="status"
                            :options="collect($statuses)->map(fn($label, $key) => (object)['key' => $key, 'label' => $label])"
                            value-field="key"
                            label-field="label"
                            :selected="request('status')"
                            placeholder="Semua"
                            empty-option="Semua"
                        />
                    </div>

                    <!-- Pencarian -->
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pencarian</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode, keterangan..."
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-1.5 px-3 rounded text-sm inline-flex items-center">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                        <a href="{{ route('pum-requests.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-1.5 px-3 rounded text-sm">
                            Reset
                        </a>
                    </div>

                    <!-- Export Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" 
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-1.5 px-3 rounded text-sm inline-flex items-center">
                            <i class="fas fa-download mr-1"></i> Export
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false"
                             x-transition
                             x-cloak
                             class="absolute right-0 mt-1 w-40 bg-white rounded shadow-lg border border-gray-200 z-50">
                            <a href="{{ route('pum-requests.export', array_merge(request()->query(), ['format' => 'excel'])) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-excel text-green-600 mr-2"></i> Excel
                            </a>
                            <a href="{{ route('pum-requests.export', array_merge(request()->query(), ['format' => 'csv'])) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-csv text-blue-600 mr-2"></i> CSV
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Status Legend -->
        <div class="bg-white px-4 py-2 border-b border-gray-200">
            <div class="flex flex-wrap items-center gap-4 text-xs">
                <span class="font-medium text-gray-600">KETERANGAN:</span>
                <span class="inline-flex items-center">
                    <i class="fas fa-times-circle text-red-500 mr-1"></i> Ditolak [{{ $requests->where('status', 'rejected')->count() }}]
                </span>
                <span class="inline-flex items-center">
                    <i class="fas fa-clock text-blue-500 mr-1"></i> Menunggu [{{ $requests->where('status', 'pending')->count() }}]
                </span>
                <span class="inline-flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i> Disetujui [{{ $requests->where('status', 'approved')->count() }}]
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
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Pengaju</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $request)
                    <tr class="hover:bg-gray-50 transition-colors">
                        @include('pum.requests.columns.no', ['requests' => $requests, 'loop' => $loop])
                        @include('pum.requests.columns.kode', ['request' => $request])
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                            {{ $request->requester->name ?? '-' }}
                        </td>
                        @include('pum.requests.columns.tanggal', ['request' => $request])
                        @include('pum.requests.columns.jumlah', ['request' => $request])
                        @include('pum.requests.columns.keterangan', ['request' => $request])
                        @include('pum.requests.columns.progress', ['request' => $request])
                        @include('pum.requests.columns.status', ['request' => $request])
                        @include('pum.requests.columns.aksi-admin', ['request' => $request])
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Tidak ada data permintaan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
