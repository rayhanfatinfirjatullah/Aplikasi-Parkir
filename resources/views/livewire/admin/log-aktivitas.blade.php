@section('page-title', 'Log Aktivitas')
@section('page-subtitle', 'Riwayat aktivitas pengguna sistem')

<div>
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari aktivitas atau nama user..."
                   class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-80 text-slate-800 dark:text-white">
        </div>
        <input wire:model.live="tanggal" type="date"
               class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-800 dark:text-white">
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">No</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">User</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Aktivitas</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($logs as $i => $log)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $logs->firstItem() + $i }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-xs text-white font-bold">
                                    {{ strtoupper(substr($log->user->nama_lengkap ?? '-', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $log->user->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $log->user->role ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $log->aktivitas }}</td>
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $log->waktu_aktivitas->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">Tidak ada log aktivitas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">{{ $logs->links() }}</div>
    </div>
</div>
