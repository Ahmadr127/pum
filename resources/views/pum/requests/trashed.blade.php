@extends('layouts.app')

@section('title', 'Sampah Permintaan Uang Muka')

@section('content')
<div class="w-full">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header -->
        <div class="px-4 py-3 bg-white border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Pengajuan yang Dihapus</h2>
            <a href="{{ route('pum-requests.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded inline-flex items-center text-sm transition-colors border border-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <form action="{{ route('pum-requests.trashed') }}" method="GET">
                <div class="flex flex-wrap items-end gap-3">
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
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pencarian</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode, keterangan..."
                               class="block w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-1.5 px-3 rounded text-sm inline-flex items-center">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                        <a href="{{ route('pum-requests.trashed') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-1.5 px-3 rounded text-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
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
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dihapus Pada</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $index => $request)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                            {{ $requests->firstItem() + $index }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                {{ $request->code }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                            {{ $request->requester->name ?? '-' }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                            {{ $request->request_date ? $request->request_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                            {{ number_format($request->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-xs text-red-600">
                            {{ $request->deleted_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center space-x-2">
                            <form action="{{ route('pum-requests.force-delete', $request->id) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium bg-red-50 px-2 py-1 rounded border border-red-200" title="Hapus Permanen">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus Permanen
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-trash-restore text-4xl mb-2 text-gray-300"></i>
                            <p>Tidak ada data di tempat sampah.</p>
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
