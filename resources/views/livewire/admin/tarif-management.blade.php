@section('page-title', 'Biaya & Status Kendaraan')
@section('page-subtitle', 'Manajemen sistem tarif dan aturan hak istimewa')

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '', deleteType: '' }">

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

    {{-- ── Navigation Tabs ── --}}
    <div class="flex gap-2 p-1 mb-6 bg-[#0F172A] border border-[#1e293b] rounded-xl w-fit">
        <button wire:click="switchTab('tarif')" 
                class="px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-200 {{ $activeTab === 'tarif' ? 'bg-[#1e293b] text-cyan-400 shadow-sm' : 'text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b]/50' }}">
            Tarif Dasar Kendaraan
        </button>
        <button wire:click="switchTab('status')" 
                class="px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-200 {{ $activeTab === 'status' ? 'bg-[#1e293b] text-cyan-400 shadow-sm' : 'text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b]/50' }}">
            Status Spesial & Hak Istimewa
        </button>
    </div>

    {{-- ========================================================= --}}
    {{-- TAB 1: TARIF KENDARAAN                                    --}}
    {{-- ========================================================= --}}
    @if($activeTab === 'tarif')
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
                                    <button @click="deleteType = 'tarif'; deleteId = {{ $tarif->id_tarif }}; deleteName = 'Tarif ' + {{ \Illuminate\Support\Js::from(ucfirst($tarif->jenis_kendaraan)) }}; showDeleteModal = true"
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
    @endif

    {{-- ========================================================= --}}
    {{-- TAB 2: STATUS KENDARAAN                                   --}}
    {{-- ========================================================= --}}
    @if($activeTab === 'status')
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="searchStatus" type="text" placeholder="Cari status..."
                       class="input-base pl-10 w-full sm:w-80">
            </div>
            <button wire:click="openCreateStatus" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Status
            </button>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Status Spesial</th>
                            <th>Hak Istimewa</th>
                            <th>Prioritas Akses</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statuses as $i => $status)
                        <tr wire:key="status-{{ $status->id_status }}">
                            <td class="text-[#94a3b8] w-12">{{ $statuses->firstItem() + $i }}</td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="badge badge-{{ $status->warna_badge }} w-fit text-xs font-bold">{{ $status->label_status }}</span>
                                    <span class="text-xs text-[#94a3b8] mt-1">{{ $status->deskripsi }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    @if($status->metode_tarif === 'reguler')
                                        <span class="text-xs font-medium text-[#94a3b8] flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Tarif Per-Jam
                                        </span>
                                    @elseif($status->metode_tarif === 'fix_per_hari')
                                        <span class="text-xs font-medium text-cyan-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Fix Harian Rp {{ number_format($status->nominal_tarif, 0, ',', '.') }}
                                        </span>
                                    @elseif($status->metode_tarif === 'membership')
                                        <span class="text-xs font-medium text-amber-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Membership Rp {{ number_format($status->nominal_tarif, 0, ',', '.') }}/bln
                                        </span>
                                    @endif

                                    @if($status->is_bebas_denda)
                                        <span class="text-xs font-medium text-emerald-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Bebas Denda Karcis
                                        </span>
                                    @else
                                        <span class="text-xs font-medium text-[#94a3b8] flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Wajib Bayar Denda
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="px-2 py-1 bg-[#1e293b] text-[#e2e8f0] text-xs rounded-md font-bold border border-[#334155]">
                                    Level {{ $status->prioritas_level }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openEditStatus({{ $status->id_status }})"
                                            class="p-2 text-cyan-400 hover:bg-cyan-500/10 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @if($status->nama_status !== 'reguler')
                                    <button @click="deleteType = 'status'; deleteId = {{ $status->id_status }}; deleteName = 'Status ' + {{ \Illuminate\Support\Js::from($status->label_status) }}; showDeleteModal = true"
                                            class="p-2 text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-[#94a3b8]">Tidak ada data status tambahan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-[#1e293b]">{{ $statuses->links() }}</div>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- MODALS                                                    --}}
    {{-- ========================================================= --}}

    {{-- Modal Tarif --}}
    @if($showModal)
    <div class="modal-backdrop">
        <div class="modal-box p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#e2e8f0]">{{ $isEdit ? 'Edit Tarif' : 'Tambah Tarif' }}</h3>
                <button wire:click="closeModal" class="p-2 text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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

    {{-- Modal Status --}}
    @if($showStatusModal)
    <div class="modal-backdrop">
        <div class="modal-box p-6 max-w-lg">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#e2e8f0]">{{ $isEditStatus ? 'Edit Status Kendaraan' : 'Tambah Status Kendaraan' }}</h3>
                <button wire:click="closeStatusModal" class="p-2 text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveStatus">
                <div class="space-y-4">
                    <div>
                        <label class="input-label">Nama / Label Status</label>
                        <input type="text" wire:model="status_label" placeholder="Contoh: VVIP, Khusus Pegawai..."
                               class="input-base @error('status_label') input-error @enderror">
                        @error('status_label') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label mb-2">Metode Tarif</label>
                            <select wire:model.live="status_metode" class="input-base @error('status_metode') input-error @enderror">
                                <option value="reguler">Tarif Normal (Per-Jam)</option>
                                <option value="fix_per_hari">Fix Per-Hari (Mis. Pegawai)</option>
                                <option value="membership">Berlangganan / Membership</option>
                            </select>
                            @error('status_metode') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                        <div x-data="{ metode: @entangle('status_metode').live }" x-show="metode !== 'reguler'"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                            <label class="input-label mb-2">Nominal Tarif (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#94a3b8] font-semibold text-sm">Rp</span>
                                <input wire:model="status_nominal" type="number" min="0" step="500"
                                       class="input-base pl-10 @error('status_nominal') input-error @enderror">
                            </div>
                            @error('status_nominal') <p class="error-msg">{{ $message }}</p> @enderror
                            <p class="text-[10px] text-[#94a3b8] mt-1" x-show="metode === 'fix_per_hari'">*Dibayar 1x setiap hari saat keluar portal.</p>
                            <p class="text-[10px] text-[#94a3b8] mt-1" x-show="metode === 'membership'" style="display:none;">*Dibayar per bulan secara prepaid oleh admin.</p>
                        </div>
                    </div>
                    <div>
                        <label class="input-label mb-2">Mendapat Kebebasan Denda Karcis?</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="status_bebas_denda" class="sr-only peer">
                            <div class="w-11 h-6 bg-[#1e293b] border border-[#334155] rounded-full peer peer-focus:ring-2 peer-focus:ring-cyan-500/50 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-[#94a3b8] peer-checked:after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500 peer-checked:border-cyan-500"></div>
                            <span class="ml-3 text-sm font-medium text-[#e2e8f0]">Ya, bebas denda karcis hilang</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="input-label mb-1 group flex items-center gap-1 cursor-help">
                                Prioritas Level
                                <svg class="w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="absolute hidden group-hover:block bg-[#020617] whitespace-nowrap text-xs border border-[#1e293b] p-2 rounded-lg -mt-10 max-w-xs z-10 text-[#e2e8f0]">Digunakan untuk akses Area Parkir (Makin besar makin eksklusif)</span>
                            </label>
                            <input type="number" wire:model="status_prioritas" min="1"
                                   class="input-base @error('status_prioritas') input-error @enderror">
                            @error('status_prioritas') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="input-label">Warna Badge Tabel</label>
                            <select wire:model="status_warna" class="input-base @error('status_warna') input-error @enderror">
                                <option value="slate">Slate (Standar)</option>
                                <option value="cyan">Cyan</option>
                                <option value="blue">Blue</option>
                                <option value="amber">Amber</option>
                                <option value="emerald">Emerald</option>
                                <option value="rose">Rose</option>
                            </select>
                            @error('status_warna') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="input-label">Deskripsi Tambahan</label>
                        <input type="text" wire:model="status_deskripsi" placeholder="Misal: Kendaraan dinas operasional..."
                               class="input-base @error('status_deskripsi') input-error @enderror">
                        @error('status_deskripsi') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-[#1e293b]">
                    <button type="button" wire:click="closeStatusModal" class="btn-secondary">Batal</button>
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
                <button type="button" @click="deleteType === 'tarif' ? $wire.delete(deleteId) : $wire.deleteStatus(deleteId); showDeleteModal = false" class="btn-danger w-full justify-center">Hapus</button>
            </div>
        </div>
    </div>

</div>
