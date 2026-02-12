<td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
    {{ ($requests->currentPage() - 1) * $requests->perPage() + $loop->iteration }}
</td>
