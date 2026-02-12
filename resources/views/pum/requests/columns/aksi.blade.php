<td class="px-3 py-2 whitespace-nowrap text-center">
    <div class="flex items-center justify-center gap-1">
        <a href="{{ route('pum-requests.show', $request) }}" 
           class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs hover:bg-indigo-200"
           title="Lihat Detail">
            <i class="fas fa-eye mr-1"></i> Detail
        </a>
        
        @if($request->status === 'new' && (auth()->user()->hasPermission('manage_pum') || $request->requester_id === auth()->id()))
        <a href="{{ route('pum-requests.edit', $request) }}" 
           class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200"
           title="Edit Pengajuan">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        @endif

        @if(in_array($request->status, ['new', 'rejected']) && (auth()->user()->hasPermission('manage_pum') || $request->requester_id === auth()->id()))
        <form action="{{ route('pum-requests.destroy', $request) }}" method="POST" class="inline" 
              onsubmit="return confirm('Yakin ingin menghapus?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200"
                    title="Hapus Pengajuan">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
        </form>
        @endif
    </div>
</td>
