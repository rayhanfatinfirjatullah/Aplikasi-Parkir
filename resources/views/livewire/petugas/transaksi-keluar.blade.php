@section('page-title', 'Kendaraan Keluar')
@section('page-subtitle', 'Proses kendaraan keluar dan perhitungan biaya')

<div>
    <div class="mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari berdasarkan plat nomor..."
                   class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-80 text-slate-800 dark:text-white">
        </div>
    </div>

    <!-- Active Parking Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Kendaraan Sedang Terparkir</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Plat Nomor</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Jenis</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Area</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu Masuk</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Durasi</th>
                        <th class="px-6 py-4 text-center font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($transaksis as $t)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-800 dark:text-white tracking-wider">{{ $t->kendaraan->plat_nomor }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ ucfirst($t->tarif->jenis_kendaraan) }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $t->areaParkir->nama_area }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            @php $durasi = max(1, (int) ceil($t->waktu_masuk->diffInMinutes(now()) / 60)); @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                {{ $durasi }} jam
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="openCheckout({{ $t->id_parkir }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs font-semibold rounded-lg shadow-lg shadow-red-500/25 hover:shadow-red-500/40 transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                                </svg>
                                Proses Keluar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">Tidak ada kendaraan yang sedang terparkir</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">{{ $transaksis->links() }}</div>
    </div>

    <!-- Checkout Modal -->
    @if($showCheckout && $checkoutData)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Konfirmasi Check-out</h3>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Plat Nomor</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ $checkoutData['plat_nomor'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Jenis Kendaraan</span>
                    <span class="text-slate-800 dark:text-white">{{ $checkoutData['jenis_kendaraan'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Area</span>
                    <span class="text-slate-800 dark:text-white">{{ $checkoutData['area'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Waktu Masuk</span>
                    <span class="text-slate-800 dark:text-white">{{ $checkoutData['waktu_masuk'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Waktu Keluar</span>
                    <span class="text-slate-800 dark:text-white">{{ $checkoutData['waktu_keluar'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Durasi</span>
                    <span class="font-semibold text-amber-600">{{ $checkoutData['durasi_jam'] }} jam</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Tarif/Jam</span>
                    <span class="text-slate-800 dark:text-white">Rp {{ number_format($checkoutData['tarif_per_jam'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl px-4 -mx-1">
                    <span class="font-bold text-emerald-800 dark:text-emerald-300 text-lg">Total Biaya</span>
                    <span class="font-bold text-emerald-800 dark:text-emerald-300 text-lg">Rp {{ number_format($checkoutData['biaya_total'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button wire:click="$set('showCheckout', false)"
                        class="px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                <button wire:click="checkout"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl shadow-lg shadow-emerald-500/25 transition-all duration-300">
                    Konfirmasi & Cetak Struk
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
