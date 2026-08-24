@section('page-title', 'Kendaraan')
@section('page-subtitle', 'Manajemen data kendaraan terdaftar')

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="relative" x-data="{
            formatPlat(val) {
                let clean = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
                let res = ''; let i = 0;
                let p1 = '';
                while (i < clean.length && p1.length < 2 && /[A-Z]/.test(clean[i])) { p1 += clean[i++]; }
                res += p1;
                if (p1.length > 0) {
                    let p2 = '';
                    while (i < clean.length && p2.length < 4 && /[0-9]/.test(clean[i])) { p2 += clean[i++]; }
                    if (p2.length > 0) {
                        res += ' ' + p2;
                        let p3 = '';
                        while (i < clean.length && p3.length < 3 && /[A-Z]/.test(clean[i])) { p3 += clean[i++]; }
                        if (p3.length > 0) res += ' ' + p3;
                    }
                }
                return res;
            }
        }">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari plat nomor atau pemilik..."
                   x-on:input="$el.value = formatPlat($el.value)"
                   class="input-base pl-10 w-full sm:w-80">
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Kendaraan
        </button>
    </div>

    {{-- ── Table ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Warna</th>
                        <th>Pemilik</th>
                        <th>Masa Aktif</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kendaraans as $i => $k)
                    <tr wire:key="kendaraan-{{ $k->id_kendaraan }}">
                        <td class="text-[#94a3b8] w-12">{{ $kendaraans->firstItem() + $i }}</td>
                        <td>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold tracking-widest text-[#e2e8f0]">{{ $k->plat_nomor }}</span>
                                @php
                                    $statusObj = $statuses->firstWhere('nama_status', $k->status_spesial);
                                @endphp
                                @if($statusObj && $statusObj->nama_status !== 'reguler')
                                <span class="badge badge-{{ $statusObj->warna_badge }} text-[10px]">{{ $statusObj->label_status }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $k->jenis_kendaraan === 'mobil' ? 'badge-blue' : 'badge-emerald' }}">
                                {{ ucfirst($k->jenis_kendaraan) }}
                            </span>
                        </td>
                        <td class="text-[#94a3b8]">{{ $k->warna }}</td>
                        <td class="text-[#e2e8f0]">{{ $k->pemilik }}</td>
                        <td>
                            @if($statusObj && $statusObj->metode_tarif === 'membership')
                                @if($k->isMembershipActive())
                                    <span class="badge badge-emerald text-[10px]">{{ $k->masa_aktif_hingga ? \Carbon\Carbon::parse($k->masa_aktif_hingga)->format('d/m/Y') : 'Lifetime' }}</span>
                                @else
                                    <span class="badge badge-rose text-[10px]">Expired</span>
                                @endif
                            @else
                                <span class="text-xs text-[#64748b]">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                @if($statusObj && $statusObj->metode_tarif === 'membership')
                                <button wire:click="openRenew({{ $k->id_kendaraan }})"
                                        class="p-2 text-amber-400 hover:bg-amber-500/10 rounded-lg transition-colors border border-transparent hover:border-amber-500/30 bg-[#0F172A]" title="Perpanjang Membership">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                @endif
                                <button wire:click="openEdit({{ $k->id_kendaraan }})"
                                        class="p-2 text-cyan-400 hover:bg-cyan-500/10 rounded-lg transition-colors border border-transparent hover:border-cyan-500/30 bg-[#0F172A]" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="deleteId = {{ $k->id_kendaraan }}; deleteName = 'Kendaraan ' + {{ \Illuminate\Support\Js::from($k->plat_nomor) }}; showDeleteModal = true"
                                        class="p-2 text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors border border-transparent hover:border-rose-500/30 bg-[#0F172A]" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-[#94a3b8]">Tidak ada data kendaraan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[#1e293b]">{{ $kendaraans->links('vendor.livewire.custom-pagination') }}</div>
    </div>

    {{-- ── Form Modal ── --}}
    @if($showModal)
    <div class="modal-backdrop">
        <div class="modal-box p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#e2e8f0]">{{ $isEdit ? 'Edit Kendaraan' : 'Tambah Kendaraan' }}</h3>
                <button wire:click="closeModal" class="p-2 text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save">
                <div class="space-y-4">
                    {{-- Plat Nomor (Alpine.js format logic preserved) --}}
                    <div x-data="{
                        formatPlat(val) {
                            let clean = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
                            let res = ''; let i = 0;
                            let p1 = '';
                            while (i < clean.length && p1.length < 2 && /[A-Z]/.test(clean[i])) { p1 += clean[i++]; }
                            res += p1;
                            if (p1.length > 0) {
                                let p2 = '';
                                while (i < clean.length && p2.length < 4 && /[0-9]/.test(clean[i])) { p2 += clean[i++]; }
                                if (p2.length > 0) {
                                    res += ' ' + p2;
                                    let p3 = '';
                                    while (i < clean.length && p3.length < 3 && /[A-Z]/.test(clean[i])) { p3 += clean[i++]; }
                                    if (p3.length > 0) res += ' ' + p3;
                                }
                            }
                            return res;
                        }
                    }">
                        <label class="input-label">Plat Nomor</label>
                        <input wire:model="plat_nomor" type="text" placeholder="Contoh: B 1234 ABC"
                               maxlength="11" x-on:input="$el.value = formatPlat($el.value)"
                               class="input-base uppercase tracking-widest font-bold text-base @error('plat_nomor') input-error @enderror">
                        @error('plat_nomor') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="input-label">Warna</label>
                        <input wire:model="warna" type="text"
                               x-data x-on:input="$el.value = $el.value.replace(/[^a-zA-Z\s]/g, '').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')"
                               class="input-base @error('warna') input-error @enderror">
                        @error('warna') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="input-label">Pemilik</label>
                        <input wire:model="pemilik" type="text"
                               x-data x-on:input="$el.value = $el.value.replace(/[^a-zA-Z\s]/g, '').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')"
                               class="input-base @error('pemilik') input-error @enderror">
                        @error('pemilik') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jenis Kendaraan Radio Cards --}}
                    <div>
                        <label class="input-label">Jenis Kendaraan</label>
                        <div class="grid grid-cols-{{ count($tarifs) }} gap-2">
                            @foreach($tarifs as $tarif)
                            <label class="relative cursor-pointer">
                                <input wire:model="jenis_kendaraan" type="radio"
                                       value="{{ $tarif->jenis_kendaraan }}" class="sr-only peer">
                                <div class="p-3 text-center rounded-xl border-2 border-[#334155] bg-[#020617] transition-all duration-200
                                            peer-checked:border-cyan-500 peer-checked:bg-cyan-500/10 hover:border-cyan-500/40">
                                    <span class="block text-sm font-bold text-[#e2e8f0]">{{ ucfirst($tarif->jenis_kendaraan) }}</span>
                                    <span class="block text-[10px] text-[#94a3b8] mt-1">
                                        Rp {{ number_format($tarif->tarif_jam_pertama, 0, ',', '.') }}/jam
                                    </span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('jenis_kendaraan') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status Spesial Radio Cards --}}
                    <div class="p-4 bg-[#020617] border border-[#1e293b] rounded-xl">
                        <label class="input-label mb-3">Status Kendaraan</label>
                        <div class="grid grid-cols-{{ min(count($statuses), 3) }} gap-2">
                            @foreach($statuses as $status)
                            @php
                                $colorClasses = [
                                    'cyan' => 'peer-checked:border-cyan-500 peer-checked:bg-cyan-500/10 hover:border-cyan-500/40 text-cyan-400',
                                    'blue' => 'peer-checked:border-blue-500 peer-checked:bg-blue-500/10 hover:border-blue-500/40 text-blue-400',
                                    'amber' => 'peer-checked:border-amber-500 peer-checked:bg-amber-500/10 hover:border-amber-500/40 text-amber-400',
                                    'emerald' => 'peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 hover:border-emerald-500/40 text-emerald-400',
                                    'rose' => 'peer-checked:border-rose-500 peer-checked:bg-rose-500/10 hover:border-rose-500/40 text-rose-400',
                                    'slate' => 'peer-checked:border-[#94a3b8] peer-checked:bg-[#1e293b] hover:border-[#94a3b8]/50 text-[#e2e8f0]',
                                ];
                                $c = $colorClasses[$status->warna_badge] ?? $colorClasses['slate'];
                                $parts = explode(' text-', $c);
                                $borderBg = $parts[0];
                                $textCls = 'text-' . $parts[1];
                            @endphp
                            <label class="relative cursor-pointer">
                                <input wire:model="status_spesial" type="radio" value="{{ $status->nama_status }}" class="sr-only peer">
                                <div class="p-3 text-center rounded-xl border-2 border-[#334155] bg-[#0F172A] transition-all duration-200 {{ $borderBg }}">
                                    <span class="block text-sm font-bold {{ $textCls }}">{{ $status->label_status }}</span>
                                    <span class="block text-[10px] text-[#94a3b8] mt-1">{{ $status->deskripsi ?? 'Tarif Normal' }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
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

    {{-- ── Form Renew Modal ── --}}
    @if($showRenewModal)
    <div class="modal-backdrop">
        <div class="modal-box p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#e2e8f0]">Perpanjang Membership</h3>
                <button wire:click="closeRenewModal" class="p-2 text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="processRenew">
                <div class="space-y-4">
                    <div>
                        <label class="input-label">Durasi Perpanjangan</label>
                        <select wire:model="renewMonths" class="input-base @error('renewMonths') input-error @enderror">
                            <option value="">Pilih Durasi</option>
                            <option value="1">1 Bulan</option>
                            <option value="3">3 Bulan</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">1 Tahun</option>
                        </select>
                        @error('renewMonths') <p class="error-msg">{{ $message }}</p> @enderror
                        <p class="text-xs text-[#94a3b8] mt-2 flex items-center gap-1.5 bg-[#020617] p-3 rounded-lg border border-[#1e293b]">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Masa aktif akan dihitung dari sisa masa aktif saat ini atau dari hari ini (jika sudah kadaluwarsa).
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-[#1e293b]">
                    <button type="button" wire:click="closeRenewModal" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Proses Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
