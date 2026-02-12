@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-1">Selamat Datang, {{ $user->name }}!</h2>
            <p class="text-gray-600">{{ $user->role->display_name ?? 'User' }} - Sistem Permintaan Uang Muka</p>
        </div>
    </div>

    {{-- Approval Stats Section - For Approvers (approve_pum) --}}
    @if(isset($approvalStats))
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-tasks text-orange-600 mr-2"></i> Tugas Approval Saya
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card 
                :value="$approvalStats['total']" 
                label="Total Tugas Approval" 
                icon="fas fa-clipboard-list" 
                color="blue" 
                :href="route('pum-approvals.index')" 
            />
            <x-stat-card 
                :value="$approvalStats['pending']" 
                label="Menunggu Approval Saya" 
                icon="fas fa-clock" 
                color="yellow" 
                :href="route('pum-approvals.index')" 
            />
            <x-stat-card 
                :value="$approvalStats['approved']" 
                label="Sudah Saya Approve" 
                icon="fas fa-check-circle" 
                color="green" 
                :href="route('pum-approvals.index')" 
            />
        </div>
    </div>
    @endif

    {{-- My Request Stats Section - For Request Creators (create_pum) --}}
    @if(isset($myRequestStats))
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-user-circle text-blue-600 mr-2"></i> Permintaan Saya
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card 
                :value="$myRequestStats['total']" 
                label="Total Permintaan Saya" 
                icon="fas fa-file-invoice-dollar" 
                color="blue" 
                :href="route('pum-requests.my-requests')" 
            />
            <x-stat-card 
                :value="$myRequestStats['pending']" 
                label="Sedang Diproses" 
                icon="fas fa-hourglass-half" 
                color="yellow" 
                :href="route('pum-requests.my-requests')" 
            />
            <x-stat-card 
                :value="$myRequestStats['completed']" 
                label="Selesai" 
                icon="fas fa-check-double" 
                color="green" 
                :href="route('pum-requests.my-requests')" 
            />
        </div>
    </div>
    @endif


    @php
        $hasApprovals = isset($pendingApprovals) || $user->hasPermission('approve_pum');
        $hasRequests = isset($recentRequests) || $user->hasPermission('manage_pum') || $user->hasPermission('create_pum');
        $showTwoColumns = $hasApprovals && $hasRequests;
    @endphp

    <div class="grid grid-cols-1 {{ $showTwoColumns ? 'lg:grid-cols-2' : '' }} gap-6">
        <!-- Pending Approvals Widget -->
        @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 bg-orange-50 border-b border-orange-100 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-bell text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-orange-900">Menunggu Persetujuan Anda</h3>
                        <p class="text-xs text-orange-600">{{ $pendingApprovalsCount }} permintaan</p>
                    </div>
                </div>
                <a href="{{ route('pum-approvals.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($pendingApprovals as $approval)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('pum-requests.show', $approval) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                {{ $approval->code }}
                            </a>
                            <p class="text-sm text-gray-500 truncate">{{ $approval->requester->name ?? '-' }}</p>
                        </div>
                        <div class="text-right mx-4">
                            <p class="font-bold text-green-600">Rp {{ number_format($approval->amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400">{{ $approval->request_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex gap-1">
                            <a href="{{ route('pum-requests.show', $approval) }}" class="p-2 bg-green-100 text-green-700 rounded hover:bg-green-200" title="Lihat & Setujui">
                                <i class="fas fa-check"></i>
                            </a>
                            <a href="{{ route('pum-requests.show', $approval) }}" class="p-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @elseif($user->hasPermission('approve_pum'))
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">Tidak Ada Approval Pending</h3>
                <p class="text-sm text-gray-500">Semua permintaan sudah diproses</p>
            </div>
        </div>
        @endif

        <!-- My Recent Requests Widget -->
        @if(isset($recentRequests) && $recentRequests->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 bg-blue-50 border-b border-blue-100 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-file-alt text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-blue-900">Permintaan Terbaru</h3>
                        <p class="text-xs text-blue-600">5 permintaan terakhir</p>
                    </div>
                </div>
                <a href="{{ route('pum-requests.my-requests') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($recentRequests as $request)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('pum-requests.show', $request) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                {{ $request->code }}
                            </a>
                            <p class="text-sm text-gray-500 truncate">{{ Str::limit($request->description, 30) ?? '-' }}</p>
                        </div>
                        <div class="text-right mx-4">
                            <p class="font-bold text-gray-900">Rp {{ number_format($request->amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400">{{ $request->created_at->format('d/m/Y') }}</p>
                        </div>
                        <x-status-badge :status="$request->status" size="sm" />
                    </div>
                </div>
                @endforeach
            </div>
            <div class="p-4 bg-gray-50 border-t">
                <a href="{{ route('pum-requests.create') }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Buat Permintaan Baru
                </a>
            </div>
        </div>
        @elseif($user->hasPermission('manage_pum') || $user->hasPermission('create_pum'))
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-invoice-dollar text-blue-500 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">Belum Ada Permintaan</h3>
                <p class="text-sm text-gray-500 mb-4">Buat permintaan uang muka pertama Anda</p>
                <a href="{{ route('pum-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Buat Permintaan
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Actions Card -->
    @php
        $quickActions = collect([
            ['permission' => 'manage_pum|create_pum', 'href' => route('pum-requests.create'), 'icon' => 'fas fa-plus', 'title' => 'Buat Permintaan', 'subtitle' => 'Ajukan uang muka', 'color' => 'green'],
            ['permission' => 'approve_pum', 'href' => route('pum-approvals.index'), 'icon' => 'fas fa-clipboard-check', 'title' => 'Approval', 'subtitle' => ($pendingApprovalsCount ?? 0) . ' menunggu', 'color' => 'orange'],
            ['permission' => 'manage_users', 'href' => route('users.index'), 'icon' => 'fas fa-users', 'title' => 'Kelola Pengguna', 'subtitle' => ($userCount ?? 0) . ' pengguna', 'color' => 'blue'],
            ['permission' => 'manage_roles', 'href' => route('roles.index'), 'icon' => 'fas fa-user-tag', 'title' => 'Kelola Role', 'subtitle' => ($roleCount ?? 0) . ' role', 'color' => 'purple'],
            ['permission' => 'manage_organization_units', 'href' => route('organization-units.index'), 'icon' => 'fas fa-building', 'title' => 'Unit Organisasi', 'subtitle' => ($orgUnitCount ?? 0) . ' unit', 'color' => 'teal'],
            ['permission' => 'manage_pum_workflows', 'href' => route('pum-workflows.index'), 'icon' => 'fas fa-project-diagram', 'title' => 'Workflow PUM', 'subtitle' => 'Kelola approval', 'color' => 'indigo'],
        ])->filter(function($action) use ($user) {
            // Handle multiple permissions separated by |
            $permissions = explode('|', $action['permission']);
            foreach ($permissions as $perm) {
                if ($user->hasPermission(trim($perm))) {
                    return true;
                }
            }
            return false;
        });
        
        $actionGridClass = match($quickActions->count()) {
            1 => 'grid-cols-1',
            2 => 'grid-cols-1 md:grid-cols-2',
            3 => 'grid-cols-1 md:grid-cols-3',
            default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        };
    @endphp
    
    @if($quickActions->count() > 0)
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="grid {{ $actionGridClass }} gap-4">
                @foreach($quickActions as $action)
                    <x-quick-action 
                        :href="$action['href']" 
                        :icon="$action['icon']" 
                        :title="$action['title']" 
                        :subtitle="$action['subtitle']" 
                        :color="$action['color']" 
                    />
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
