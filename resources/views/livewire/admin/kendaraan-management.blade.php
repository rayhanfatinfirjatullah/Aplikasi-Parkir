@section('page-title', 'Kendaraan')
@section('page-subtitle', 'Manajemen data kendaraan')

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari plat nomor atau pemilik..."
                   class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-80 text-slate-800 dark:text-white">
        </div>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Kendaraan
        </button>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">No</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Plat Nomor</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Warna</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-600 dark:text-slate-300">Pemilik</th>
                        <th class="px-6 py-4 text-center font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($kendaraans as $i => $k)
                    <tr wire:key="kendaraan-{{ $k->id_kendaraan }}" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $kendaraans->firstItem() + $i }}</td>
                        <td class="px-6 py-4 font-bold text-slate-800 dark:text-white tracking-wider">
                            {{ $k->plat_nomor }}
                            @if($k->status_spesial === 'vvip')
                            <span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 rounded-full text-[10px] font-bold uppercase tracking-wider border border-amber-200 dark:border-amber-800">VVIP</span>
                            @elseif($k->status_spesial === 'vip')
                            <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 rounded-full text-[10px] font-bold uppercase tracking-wider border border-blue-200 dark:border-blue-800">VIP</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $k->warna }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $k->pemilik }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openEdit({{ $k->id_kendaraan }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="deleteId = {{ $k->id_kendaraan }}; deleteName = 'Kendaraan ' + {{ \Illuminate\Support\Js::from($k->plat_nomor) }}; showDeleteModal = true"
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
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">Tidak ada data kendaraan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">{{ $kendaraans->links() }}</div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">{{ $isEdit ? 'Edit Kendaraan' : 'Tambah Kendaraan' }}</h3>
            <form wire:submit="save">
                <div class="space-y-4">
                    <div x-data="{
                            formatPlat(el) {
                                let val = el.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                                let res = '';
                                let i = 0;
                                
                                let p1 = '';
                                while (i < val.length && p1.length < 2 && /[A-Z]/.test(val[i])) {
                                    p1 += val[i++];
                                }
                                res += p1;
                                
                                let p2 = '';
                                while (i < val.length && p2.length < 4 && /[0-9]/.test(val[i])) {
                                    p2 += val[i++];
                                }
                                if (p2.length > 0) res += (res.length > 0 ? ' ' : '') + p2;
                                
                                let p3 = '';
                                while (i < val.length && p3.length < 3 && /[A-Z]/.test(val[i])) {
                                    p3 += val[i++];
                                }
                                if (p3.length > 0) res += (res.length > 0 ? ' ' : '') + p3;
                                
                                el.value = res;
                            }
                        }">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Plat Nomor</label>
                        <input wire:model="plat_nomor" type="text" placeholder="Contoh: B 1234 ABC" maxlength="11" x-on:input="formatPlat($el)" oninput="this.value = this.value.toUpperCase()" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 uppercase @error('plat_nomor') border-red-500 @enderror">
                        @error('plat_nomor') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Warna</label>
                        <input wire:model="warna" type="text" x-data x-on:input="$el.value = $el.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('warna') border-red-500 @enderror">
                        @error('warna') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pemilik</label>
                        <input wire:model="pemilik" type="text" x-data x-on:input="$el.value = $el.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('pemilik') border-red-500 @enderror">
                        @error('pemilik') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <!-- Status Spesial -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-600 rounded-xl">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Status Kendaraan</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="relative cursor-pointer">
                                <input wire:model="status_spesial" type="radio" value="reguler" class="sr-only peer">
                                <div class="p-3 text-center rounded-xl border-2 transition-all peer-checked:border-slate-500 peer-checked:bg-slate-100 dark:peer-checked:bg-slate-600 border-slate-200 dark:border-slate-600 hover:border-slate-300">
                                    <span class="block text-sm font-bold text-slate-700 dark:text-slate-300">Reguler</span>
                                    <span class="block text-[10px] text-slate-400 mt-1">Tarif Normal</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input wire:model="status_spesial" type="radio" value="vip" class="sr-only peer">
                                <div class="p-3 text-center rounded-xl border-2 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 border-slate-200 dark:border-slate-600 hover:border-blue-300">
                                    <span class="block text-sm font-bold text-blue-700 dark:text-blue-400">VIP</span>
                                    <span class="block text-[10px] text-slate-400 mt-1">Gratis, Denda Tetap</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input wire:model="status_spesial" type="radio" value="vvip" class="sr-only peer">
                                <div class="p-3 text-center rounded-xl border-2 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 border-slate-200 dark:border-slate-600 hover:border-amber-300">
                                    <span class="block text-sm font-bold text-amber-700 dark:text-amber-400">VVIP</span>
                                    <span class="block text-[10px] text-slate-400 mt-1">Gratis Total 100%</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" wire:click="closeModal"
                            class="px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/25 transition-all duration-300">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center"
             x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.away="showDeleteModal = false">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Konfirmasi Penghapusan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Apakah Anda yakin ingin menghapus <strong class="font-bold text-red-600 dark:text-red-400" x-text="deleteName"></strong> secara permanen? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex justify-center gap-3">
                <button type="button" @click="showDeleteModal = false"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors w-full">
                    Batal
                </button>
                <button type="button" @click="$wire.delete(deleteId); showDeleteModal = false"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-xl shadow-lg shadow-red-600/25 hover:bg-red-700 transition-colors w-full">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
