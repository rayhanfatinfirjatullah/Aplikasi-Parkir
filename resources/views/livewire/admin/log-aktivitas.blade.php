@section('page-title', 'Log Aktivitas')
@section('page-subtitle', 'Riwayat aktivitas pengguna sistem')

<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari aktivitas atau nama user..."
                   class="input-base pl-10">
        </div>
        <input wire:model.live="tanggal" type="date"
               class="input-base w-auto">
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $i => $log)
                    <tr>
                        <td class="text-[#94a3b8] w-12">{{ $logs->firstItem() + $i }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-xs text-white font-bold shrink-0">
                                    {{ strtoupper(substr($log->user->nama_lengkap ?? '-', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-[#e2e8f0] text-sm">{{ $log->user->nama_lengkap ?? '-' }}</p>
                                    <p class="text-[10px] text-[#94a3b8] uppercase tracking-wider">{{ $log->user->role ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-[#e2e8f0]">{{ $log->aktivitas }}</td>
                        <td class="text-[#94a3b8] font-mono tabular-nums text-xs">{{ $log->waktu_aktivitas->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center text-[#94a3b8]">
                            <svg class="w-10 h-10 mx-auto mb-3 text-[#334155]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Tidak ada log aktivitas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[#1e293b]">{{ $logs->links('vendor.livewire.custom-pagination') }}</div>
    </div>
</div>
