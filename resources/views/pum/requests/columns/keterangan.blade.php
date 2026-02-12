<td class="px-3 py-2 text-sm text-gray-900 max-w-xs truncate" title="{{ $request->description }}">
    {{ Str::limit($request->description, 40) ?? '-' }}
</td>
