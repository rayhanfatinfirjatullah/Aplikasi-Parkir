@section('page-title', 'Tarif Parkir')
@section('page-subtitle', 'Manajemen tarif parkir per jenis kendaraan')

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }">

    {{-- ── Global Settings ── --}}
    <div class="card p-6 mb-6">
        <h3 class="text-base font-bold text-[#e2e8f0] mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Pengaturan Biaya Tambahan
        </h3>
        <form wire:submit="updateDenda" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                <label class="input-label">Denda Karcis Hilang (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#94a3b8] font-semibold text-sm">Rp</span>
                    <input wire:model="denda_karcis_hilang" type="number" min="0" step="500" required
                           class="input-base pl-10 @error('denda_karcis_hilang') input-error @enderror">
                </div>
                @error('denda_karcis_hilang') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary shrink-0">
                <span wire:loading.remove wire:target="updateDenda">Update Denda</span>
                <span wire:loading wire:target="updateDenda">Menyimpan...</span>
            </button>
        </form>
    </div>

    {{-- ── Toolbar ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari tarif..."
                   class="input-base pl-10 w-full sm:w-80">
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Tarif
        </button>
    </div>

    {{-- ── Table ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Kendaraan</th>
                        <th>Tarif per Jam</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifs as $i => $tarif)
                    <tr wire:key="tarif-{{ $tarif->id_tarif }}">
                        <td class="text-[#94a3b8] w-12">{{ $tarifs->firstItem() + $i }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full
                                    @if($tarif->jenis_kendaraan === 'motor') bg-cyan-400
                                    @elseif($tarif->jenis_kendaraan === 'mobil') bg-emerald-400
                                    @else bg-amber-400 @endif">
                                </span>
                                <span class="font-semibold text-[#e2e8f0]">{{ ucfirst($tarif->jenis_kendaraan) }}</span>
                            </div>
                        </td>
                        <td class="font-bold text-cyan-400 tabular-nums">
                            Rp {{ number_format($tarif->tarif_per_jam, 0, ',', '.') }}
                            <span class="text-xs font-normal text-[#94a3b8]">/jam</span>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="openEdit({{ $tarif->id_tarif }})"
                                        class="p-2 text-cyan-400 hover:bg-cyan-500/10 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="deleteId = {{ $tarif->id_tarif }}; deleteName = 'Tarif ' + {{ \Illuminate\Support\Js::from(ucfirst($tarif->jenis_kendaraan)) }}; showDeleteModal = true"
                                        class="p-2 text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center text-[#94a3b8]">Tidak ada data tarif</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[#1e293b]">{{ $tarifs->links() }}</div>
    </div>

    {{-- ── Form Modal ── --}}
    @if($showModal)
    <div class="modal-backdrop">
        <div class="modal-box p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#e2e8f0]">{{ $isEdit ? 'Edit Tarif' : 'Tambah Tarif' }}</h3>
                <button wire:click="closeModal" class="p-2 text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save">
                <div class="space-y-4">
                    <div>
                        <label class="input-label">Jenis Kendaraan</label>
                        <input type="text" wire:model="jenis_kendaraan" list="jenis-kendaraan-list"
                               placeholder="Ketik atau pilih jenis kendaraan..."
                               class="input-base @error('jenis_kendaraan') input-error @enderror">
                        @error('jenis_kendaraan') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label">Tarif per Jam (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#94a3b8] font-semibold text-sm">Rp</span>
                            <input wire:model="tarif_per_jam" type="number" step="500" min="0"
                                   class="input-base pl-10 @error('tarif_per_jam') input-error @enderror">
                        </div>
                        @error('tarif_per_jam') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-[#1e293b]">
                    <button type="button" wire:click="closeModal" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ── Delete Modal ── --}}
    <div x-show="showDeleteModal" style="display: none;"
         class="fixed inset-0 z-[60] flex items-center justify-center bg-[#020617]/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-[#0F172A] border border-[#1e293b] rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center"
             x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             @click.away="showDeleteModal = false">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                <svg class="w-7 h-7 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[#e2e8f0] mb-2">Konfirmasi Penghapusan</h3>
            <p class="text-sm text-[#94a3b8] mb-6">Yakin ingin menghapus <strong class="text-rose-400" x-text="deleteName"></strong> secara permanen?</p>
            <div class="flex gap-3">
                <button type="button" @click="showDeleteModal = false" class="btn-secondary w-full justify-center">Batal</button>
                <button type="button" @click="$wire.delete(deleteId); showDeleteModal = false" class="btn-danger w-full justify-center">Hapus</button>
            </div>
        </div>
    </div>

</div>
