<td class="px-3 py-2 text-center align-middle">
    @if($request->workflow)
        @php
            // Prepare data for AlpineJS
            $stepsData = $request->workflow->steps->map(function($step) use ($request) {
                $approval = $request->approvals->where('step_id', $step->id)->first();
                $status = $approval ? $approval->status : 'pending';
                
                // Default State (Future/Waiting)
                $bgColor = 'bg-gray-100';
                $textColor = 'text-gray-500';
                $borderColor = 'border-gray-200';
                $statusLabel = 'Menunggu';
                $approverName = '-';
                $approverPosition = '-';
                $respondedAt = '-';
                $note = null;
                $hoverClass = 'hover:bg-gray-200';

                // Calculate Position/Jabatan
                if ($approval && $approval->approver) {
                    $user = $approval->approver;
                    if ($user->organizationUnit) {
                        $unit = $user->organizationUnit;
                        $type = $unit->type;
                        $isHead = $unit->head_id == $user->id;
                        
                        $prefix = $isHead ? 'Kepala' : 'Staf';
                        $typeName = $type ? $type->display_name : '';
                        
                        $approverPosition = trim("$prefix $typeName $unit->name");
                    } else {
                        $approverPosition = $user->role->display_name ?? '-';
                    }
                } else {
                    // Future/Pending step - estimate position based on step config
                    if ($step->approver_type == 'role') {
                        $approverPosition = $step->role->display_name ?? 'Role';
                    } elseif ($step->approver_type == 'organization_head') {
                        $approverPosition = 'Kepala Unit Organisasi'; // General title
                    } elseif ($step->approver_type == 'user') {
                        $approverPosition = $step->user->name ?? 'User Tertentu';
                    }
                }

                // Determine State
                if ($status === 'approved') {
                    $bgColor = 'bg-green-600';
                    $textColor = 'text-white';
                    $borderColor = 'border-green-600';
                    $statusLabel = 'Disetujui';
                    $approverName = $approval->approver->name ?? '-';
                    $respondedAt = $approval->responded_at ? $approval->responded_at->format('d M Y H:i') : '-';
                    $note = $approval->notes;
                    $hoverClass = 'hover:bg-green-700';
                } elseif ($status === 'rejected') {
                    $bgColor = 'bg-red-600';
                    $textColor = 'text-white';
                    $borderColor = 'border-red-600';
                    $statusLabel = 'Ditolak';
                    $approverName = $approval->approver->name ?? '-';
                    $respondedAt = $approval->responded_at ? $approval->responded_at->format('d M Y H:i') : '-';
                    $note = $approval->notes;
                    $hoverClass = 'hover:bg-red-700';
                } elseif ($status === 'pending' && $request->current_step_order == $step->order) {
                    $bgColor = 'bg-blue-600';
                    $textColor = 'text-white';
                    $borderColor = 'border-blue-600';
                    $statusLabel = 'Sedang Diproses';
                    $hoverClass = 'hover:bg-blue-700';
                }

                return [
                    'id' => $step->id,
                    'name' => $step->name,
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'bgColor' => $bgColor,
                    'textColor' => $textColor,
                    'borderColor' => $borderColor,
                    'icon' => /* Icon not used */ null,
                    'approverName' => $approverName,
                    'approverPosition' => $approverPosition,
                    'respondedAt' => $respondedAt,
                    'note' => $note,
                    'isCurrent' => $request->current_step_order == $step->order,
                    'hoverClass' => $hoverClass
                ];
            });
        @endphp

        <div x-data="{ 
            open: false, 
            modalStep: {},
            steps: {{ json_encode($stepsData) }},
            showStep(step) {
                this.modalStep = step;
                this.open = true;
            }
        }" class="flex items-center gap-1 justify-start flex-wrap">
            
            {{-- Badge UI --}}
            @foreach($stepsData as $index => $step)
                <div class="px-2 py-1 rounded text-[10px] font-medium transition-colors cursor-pointer border shadow-sm {{ $step['bgColor'] }} {{ $step['textColor'] }} {{ $step['borderColor'] }} {{ $step['hoverClass'] }}"
                     @click="showStep(steps[{{ $index }}])"
                     title="{{ $step['name'] }}: {{ $step['statusLabel'] }}">
                    
                    {{ $step['name'] }}
                </div>
            @endforeach

            {{-- Include Modal Component --}}
            @include('pum.requests.modals.modal-progress')
        </div>
    @else
        <span class="text-xs text-gray-400">-</span>
    @endif
</td>
