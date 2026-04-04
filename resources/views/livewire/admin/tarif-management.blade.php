@section('page-title', 'Tarif Parkir')
@section('page-subtitle', 'Manajemen tarif parkir per jenis kendaraan')

<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari tarif..."
                   class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-80 text-slate-800 dark:text-white">
        </div>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Tarif
        </button>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">No</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Jenis Kendaraan</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Tarif per Jam</th>
                        <th class="px-6 py-4 text-center font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($tarifs as $i => $tarif)
                    <tr wire:key="tarif-{{ $tarif->id_tarif }}" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $tarifs->firstItem() + $i }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800 dark:text-white">
                            <span class="inline-flex items-center gap-2">
                                @if($tarif->jenis_kendaraan === 'motor')
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                @elseif($tarif->jenis_kendaraan === 'mobil')
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                @else
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                @endif
                                {{ ucfirst($tarif->jenis_kendaraan) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-semibold">Rp {{ number_format($tarif->tarif_per_jam, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openEdit({{ $tarif->id_tarif }})"
                                        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $tarif->id_tarif }})" wire:confirm="Yakin ingin menghapus tarif ini?"
                                        class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">Tidak ada data tarif</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $tarifs->links() }}
        </div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">{{ $isEdit ? 'Edit Tarif' : 'Tambah Tarif' }}</h3>
            <form wire:submit="save">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis Kendaraan</label>
                        <select wire:model="jenis_kendaraan" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        @error('jenis_kendaraan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tarif per Jam (Rp)</label>
                        <input wire:model="tarif_per_jam" type="number" step="500" min="0" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                        @error('tarif_per_jam') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/25 transition-all duration-300">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
