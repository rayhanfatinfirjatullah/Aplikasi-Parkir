@section('page-title', 'Laporan Transaksi')
@section('page-subtitle', 'Lihat dan filter laporan transaksi parkir')

<div>
    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-6">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4">Filter Laporan</h3>
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Tanggal Mulai</label>
                <input wire:model="tanggal_mulai" type="date"
                       class="px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Tanggal Selesai</label>
                <input wire:model="tanggal_selesai" type="date"
                       class="px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white">
            </div>
            <div class="relative">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Cari Plat Nomor</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari..."
                       class="px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white w-48">
            </div>
            <button wire:click="filter"
                    class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-300">
                Filter
            </button>
            <button wire:click="resetFilter"
                    class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-200 transition-colors">
                Reset
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Transaksi</p>
                    <p class="text-3xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($totalTransaksi) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">No</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Plat Nomor</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Jenis</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Area</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu Masuk</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu Keluar</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Durasi</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Biaya</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($transaksis as $i => $t)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $transaksis->firstItem() + $i }}</td>
                        <td class="px-6 py-4 font-bold text-slate-800 dark:text-white tracking-wider">{{ $t->kendaraan->plat_nomor }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ ucfirst($t->tarif->jenis_kendaraan) }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $t->areaParkir->nama_area }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $t->waktu_keluar ? $t->waktu_keluar->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $t->durasi_jam ? $t->durasi_jam . ' jam' : '-' }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">{{ $t->biaya_total ? 'Rp ' . number_format($t->biaya_total, 0, ',', '.') : '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $t->status === 'masuk' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">Tidak ada data transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">{{ $transaksis->links() }}</div>
    </div>
</div>
