@section('page-title', 'Area Parkir')
@section('page-subtitle', 'Manajemen area parkir dan kapasitas')

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari area..."
                   class="input-base pl-10 w-full sm:w-80">
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Area
        </button>
    </div>

    {{-- ── Area Cards Grid ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
        @foreach($areas as $area)
        @php $pct = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas) * 100 : 0; @endphp
        <div wire:key="area-{{ $area->id_area }}"
             class="card p-5 hover:-translate-y-0.5 hover:border-cyan-500/30 transition-all duration-300">

            {{-- Card Header --}}
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#e2e8f0]">{{ $area->nama_area }}</h3>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="badge badge-blue text-[10px]" title="{{ $area->format_tipe_kendaraan }}">
                            {{ Str::limit($area->format_tipe_kendaraan, 18) }}
                        </span>
                        @php
                            $statusObj = collect($statuses)->firstWhere('nama_status', $area->level_akses);
                            $badgeClass = $statusObj && $statusObj->nama_status !== 'reguler' ? 'badge-' . $statusObj->warna_badge : 'bg-[#1e293b] text-[#94a3b8] border border-[#334155]';
                        @endphp
                        <span class="badge {{ $badgeClass }} text-[10px]">
                            {{ $statusObj ? strtoupper($statusObj->label_status) : strtoupper($area->level_akses) }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-1 shrink-0 ml-2">
                    <button wire:click="openEdit({{ $area->id_area }})"
                            class="p-1.5 text-cyan-400 hover:bg-cyan-500/10 rounded-lg transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button @click="deleteId = {{ $area->id_area }}; deleteName = 'Area ' + {{ \Illuminate\Support\Js::from($area->nama_area) }}; showDeleteModal = true"
                            class="p-1.5 text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                <div class="bg-[#020617] rounded-xl p-2.5 border border-[#1e293b]">
                    <p class="text-lg font-extrabold text-[#e2e8f0]">{{ $area->kapasitas }}</p>
                    <p class="text-[10px] text-[#94a3b8] uppercase tracking-wide mt-0.5">Kapasitas</p>
                </div>
                <div class="bg-[#020617] rounded-xl p-2.5 border border-[#1e293b]">
                    <p class="text-lg font-extrabold text-amber-400">{{ $area->terisi }}</p>
                    <p class="text-[10px] text-[#94a3b8] uppercase tracking-wide mt-0.5">Terisi</p>
                </div>
                <div class="bg-[#020617] rounded-xl p-2.5 border border-[#1e293b]">
                    <p class="text-lg font-extrabold {{ $area->sisa_kapasitas > 0 ? 'text-cyan-400' : 'text-rose-400' }}">
                        {{ $area->sisa_kapasitas }}
                    </p>
                    <p class="text-[10px] text-[#94a3b8] uppercase tracking-wide mt-0.5">Tersedia</p>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="progress-track">
                <div class="{{ $pct > 80 ? 'progress-fill-full' : ($pct > 50 ? 'progress-fill-warning' : 'progress-fill-ok') }}"
                     style="width: {{ $pct }}%"></div>
            </div>
            <p class="text-[10px] text-[#94a3b8] mt-1.5 text-right">{{ number_format($pct, 0) }}% terisi</p>
        </div>
        @endforeach
    </div>

    {{-- ── Form Modal ── --}}
    @if($showModal)
    <div class="modal-backdrop">
        <div class="modal-box p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#e2e8f0]">{{ $isEdit ? 'Edit Area' : 'Tambah Area' }}</h3>
                <button wire:click="closeModal" class="p-2 text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save">
                <div class="space-y-4">
                    <div>
                        <label class="input-label">Nama Area</label>
                        <input wire:model="nama_area" type="text"
                               class="input-base @error('nama_area') input-error @enderror">
                        @error('nama_area') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label">Kapasitas (slot)</label>
                        <input wire:model="kapasitas" type="number" min="1"
                               class="input-base @error('kapasitas') input-error @enderror">
                        @error('kapasitas') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tipe Kendaraan Checkboxes (Alpine.js preserved) --}}
                    <div x-data="{
                        tipe: @entangle('tipe_kendaraan'),
                        allOptions: {{ \Illuminate\Support\Js::from(\App\Models\Tarif::pluck('jenis_kendaraan')->toArray()) }},
                        toggleSemua(e) {
                            if (e.target.checked) {
                                this.tipe = ['semua', ...this.allOptions];
                            } else {
                                this.tipe = [];
                            }
                        },
                        toggleItem() {
                            this.$nextTick(() => {
                                let allSelected = this.allOptions.every(opt => this.tipe.includes(opt));
                                if (allSelected && !this.tipe.includes('semua')) {
                                    this.tipe.push('semua');
                                } else if (!allSelected && this.tipe.includes('semua')) {
                                    this.tipe = this.tipe.filter(i => i !== 'semua');
                                }
                            });
                        }
                    }">
                        <label class="input-label">Tipe Kendaraan (Bisa Pilih Lebih Dari 1)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            {{-- Semua Jenis --}}
                            <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                   :class="tipe.includes('semua') ? 'bg-cyan-500/10 border-cyan-500/40 text-cyan-400' : 'bg-[#020617] border-[#334155] text-[#94a3b8] hover:border-cyan-500/30'">
                                <input type="checkbox" x-model="tipe" value="semua" @change="toggleSemua"
                                       class="w-4 h-4 text-cyan-500 bg-[#020617] border-[#334155] rounded focus:ring-cyan-500/30">
                                <span class="text-sm font-medium">Semua Jenis</span>
                            </label>
                            @foreach(\App\Models\Tarif::all() as $tarif)
                            <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                   :class="tipe.includes('{{ $tarif->jenis_kendaraan }}') ? 'bg-cyan-500/10 border-cyan-500/40 text-cyan-400' : 'bg-[#020617] border-[#334155] text-[#94a3b8] hover:border-cyan-500/30'">
                                <input type="checkbox" x-model="tipe" value="{{ $tarif->jenis_kendaraan }}" @change="toggleItem"
                                       class="w-4 h-4 text-cyan-500 bg-[#020617] border-[#334155] rounded focus:ring-cyan-500/30">
                                <span class="text-sm font-medium">{{ ucfirst($tarif->jenis_kendaraan) }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('tipe_kendaraan') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="input-label">Level Akses Minimum</label>
                        <select wire:model="level_akses" class="input-base @error('level_akses') input-error @enderror">
                            @foreach($statuses as $status)
                            <option value="{{ $status->nama_status }}">
                                {{ $status->label_status }} (Min. Level {{ $status->prioritas_level }})
                            </option>
                            @endforeach
                        </select>
                        @error('level_akses') <p class="error-msg">{{ $message }}</p> @enderror
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

    {{-- ── Capacity Warning Modal ── --}}
    @if($showCapacityWarning)
    <div class="fixed inset-0 z-[70] flex items-center justify-center bg-[#020617]/70 backdrop-blur-sm"
         x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)">
        <div class="bg-[#0F172A] border border-[#1e293b] rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 text-center transform transition-all duration-300 relative overflow-hidden"
             x-show="show"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4">
            
            <!-- Background Glow -->
            <div class="absolute -top-16 -left-16 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -right-16 w-32 h-32 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-gradient-to-br from-amber-500/20 to-amber-500/5 border border-amber-500/30 flex items-center justify-center relative shadow-[0_0_15px_rgba(245,158,11,0.2)]">
                <!-- Animate Ping -->
                <div class="absolute inset-0 rounded-full animate-ping bg-amber-500/30" style="animation-duration: 2s;"></div>
                <svg class="w-10 h-10 text-amber-400 relative z-10 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-[#e2e8f0] mb-3">Kapasitas Tidak Valid!</h3>
            
            <div class="bg-[#020617]/80 rounded-xl p-4 border border-[#1e293b] mb-6 text-left relative z-10">
                <p class="text-sm text-[#94a3b8] mb-4 leading-relaxed">
                    Kapasitas baru (<strong class="text-rose-400">{{ $capacityErrorData['kapasitas_input'] ?? '' }}</strong>) tidak dapat diterapkan pada area <strong class="text-cyan-400">{{ $capacityErrorData['nama_area'] ?? '' }}</strong>. 
                    Terdapat kendaraan yang sedang parkir melebihi kapasitas yang Anda masukkan.
                </p>
                <div class="flex items-center justify-between bg-[#0F172A] rounded-lg p-3.5 border border-[#334155] shadow-inner">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse shadow-[0_0_8px_rgba(251,191,36,0.6)]"></div>
                        <span class="text-sm font-medium text-[#94a3b8]">Kendaraan Terparkir:</span>
                    </div>
                    <span class="text-lg font-black text-amber-400">{{ $capacityErrorData['terisi'] ?? '' }} <span class="text-xs font-normal text-[#94a3b8]">Unit</span></span>
                </div>
                <p class="text-[11px] text-[#64748b] mt-3 italic text-center">
                    *Harap kurangi jumlah kendaraan terparkir terlebih dahulu atau masukkan angka minimal {{ $capacityErrorData['terisi'] ?? '' }}.
                </p>
            </div>

            <button type="button" wire:click="closeCapacityWarning" class="btn-primary w-full justify-center shadow-lg shadow-cyan-500/20 py-2.5 text-sm uppercase tracking-wider font-bold">
                Mengerti & Kembali
            </button>
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
