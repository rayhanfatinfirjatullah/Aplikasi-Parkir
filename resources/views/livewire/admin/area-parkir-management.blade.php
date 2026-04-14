@section('page-title', 'Area Parkir')
@section('page-subtitle', 'Manajemen area parkir dan kapasitas')

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari area..."
                   class="pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-80 text-slate-800 dark:text-white">
        </div>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Area
        </button>
    </div>

    <!-- Area Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @foreach($areas as $area)
        <div wire:key="area-{{ $area->id_area }}" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $area->nama_area }}</h3>
                <div class="flex gap-1">
                    <button wire:click="openEdit({{ $area->id_area }})" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button @click="deleteId = {{ $area->id_area }}; deleteName = 'Area ' + {{ \Illuminate\Support\Js::from($area->nama_area) }}; showDeleteModal = true" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex gap-2 mb-4">
                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-600 uppercase tracking-wider">
                    Kendaraan: {{ $area->tipe_kendaraan }}
                </span>
                <span class="px-2.5 py-1 {{ $area->level_akses == 'vvip' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 border-amber-200 dark:border-amber-800' : ($area->level_akses == 'vip' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 border-blue-200 dark:border-blue-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600') }} text-xs font-semibold rounded-lg border uppercase tracking-wider">
                    Akses: {{ $area->level_akses }}
                </span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">Kapasitas</span>
                    <span class="font-semibold text-slate-800 dark:text-white">{{ $area->kapasitas }} slot</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">Terisi</span>
                    <span class="font-semibold text-slate-800 dark:text-white">{{ $area->terisi }} slot</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">Tersedia</span>
                    <span class="font-semibold {{ $area->sisa_kapasitas > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $area->sisa_kapasitas }} slot</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 mt-2">
                    @php $pct = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas) * 100 : 0; @endphp
                    <div class="h-3 rounded-full transition-all duration-500 {{ $pct > 80 ? 'bg-red-500' : ($pct > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="px-2 py-4">{{ $areas->links() }}</div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">{{ $isEdit ? 'Edit Area' : 'Tambah Area' }}</h3>
            <form wire:submit="save">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Area</label>
                        <input wire:model="nama_area" type="text" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('nama_area') border-red-500 @enderror">
                        @error('nama_area') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kapasitas</label>
                        <input wire:model="kapasitas" type="number" min="1" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('kapasitas') border-red-500 @enderror">
                        @error('kapasitas') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe Kendaraan</label>
                        <select wire:model="tipe_kendaraan" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('tipe_kendaraan') border-red-500 @enderror">
                            <option value="semua">Semua</option>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                        </select>
                        @error('tipe_kendaraan') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Level Akses Minimum</label>
                        <select wire:model="level_akses" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('level_akses') border-red-500 @enderror">
                            <option value="reguler">Reguler</option>
                            <option value="vip">VIP</option>
                            <option value="vvip">VVIP</option>
                        </select>
                        @error('level_akses') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
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
