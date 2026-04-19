@section('page-title', 'Kendaraan Masuk')
@section('page-subtitle', 'Catat kendaraan masuk area parkir')

<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Form Check-in ── --}}
        <div class="lg:col-span-2">
            <div class="card p-6">
                <h3 class="text-base font-bold text-[#e2e8f0] mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-5 bg-emerald-400 rounded-full"></span>
                    Form Check-in Kendaraan
                </h3>

                <form wire:submit="checkin">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Plat Nomor (Alpine autocomplete, fully preserved) --}}
                        <div class="md:col-span-2 relative"
                             x-data="{
                                 open: false,
                                 search: @entangle('plat_nomor'),
                                 vehicles: {{ Js::from($registeredVehicles) }},
                                 get filteredVehicles() {
                                     if (!this.search || this.search.length < 1) return [];
                                     let s = this.search.toString().replace(/[^A-Z0-9]/gi, '').toLowerCase();
                                     return this.vehicles.filter(v => v.plat_nomor.replace(/[^A-Z0-9]/gi, '').toLowerCase().includes(s)).slice(0, 5);
                                 },
                                 selectVehicle(vehicle) {
                                     this.search = vehicle.plat_nomor;
                                     this.open = false;
                                     $wire.fillVehicleData();
                                 },
                                 formatPlat(val) {
                                     let clean = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
                                     let res = ''; let i = 0;
                                     let p1 = '';
                                     while (i < clean.length && p1.length < 2 && /[A-Z]/.test(clean[i])) { p1 += clean[i++]; }
                                     res += p1;
                                     let p2 = '';
                                     while (i < clean.length && p2.length < 4 && /[0-9]/.test(clean[i])) { p2 += clean[i++]; }
                                     if (p2.length > 0) res += (res.length > 0 ? ' ' : '') + p2;
                                     let p3 = '';
                                     if (p2.length > 0) {
                                         while (i < clean.length && p3.length < 3 && /[A-Z]/.test(clean[i])) { p3 += clean[i++]; }
                                         if (p3.length > 0) res += ' ' + p3;
                                     }
                                     return res;
                                 }
                             }" @click.away="open = false">
                            <label class="input-label">Plat Nomor</label>
                            <div class="relative">
                                <input x-model="search"
                                       x-on:input="search = formatPlat($event.target.value); open = true"
                                       x-on:focus="open = true"
                                       @keydown.escape.window="open = false"
                                       type="text" placeholder="Contoh: B 1234 ABC"
                                       maxlength="11" autocomplete="off"
                                       class="input-base text-lg font-bold tracking-widest uppercase @error('plat_nomor') input-error @enderror">

                                {{-- Autocomplete Dropdown --}}
                                <div x-show="open && filteredVehicles.length > 0"
                                     x-transition.opacity
                                     class="absolute z-50 w-full mt-2 bg-[#0F172A] rounded-xl shadow-2xl border border-[#1e293b] overflow-hidden"
                                     style="display: none;">
                                    <ul class="max-h-60 overflow-auto divide-y divide-[#1e293b]">
                                        <template x-for="v in filteredVehicles" :key="v.id_kendaraan">
                                            <li @click="selectVehicle(v)"
                                                class="p-3 hover:bg-[#1e293b] cursor-pointer transition-colors flex justify-between items-center group">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0">
                                                        <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-[#e2e8f0] tracking-wider text-sm" x-text="v.plat_nomor"></p>
                                                        <p class="text-xs text-[#94a3b8]" x-text="v.warna + ' — ' + v.pemilik"></p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <template x-if="v.status_spesial === 'vvip'">
                                                        <span class="badge badge-amber text-[10px]">VVIP</span>
                                                    </template>
                                                    <template x-if="v.status_spesial === 'vip'">
                                                        <span class="badge badge-cyan text-[10px]">VIP</span>
                                                    </template>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @error('plat_nomor') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>

                        {{-- Warna --}}
                        <div>
                            <label class="input-label">Warna Kendaraan</label>
                            <input wire:model="warna" type="text" placeholder="Contoh: Hitam"
                                   x-data x-on:input="$el.value = $el.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')"
                                   class="input-base @error('warna') input-error @enderror">
                            @error('warna') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pemilik --}}
                        <div>
                            <label class="input-label">Pemilik</label>
                            <input wire:model="pemilik" type="text" placeholder="Nama pemilik kendaraan"
                                   x-data x-on:input="$el.value = $el.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')"
                                   class="input-base @error('pemilik') input-error @enderror">
                            @error('pemilik') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jenis Kendaraan --}}
                        {{-- Jenis Kendaraan --}}
                        <div class="relative" x-data="{
                            open: false,
                            search: '',
                            jenis: @entangle('jenis_kendaraan').live,
                            tarifs: {{ Js::from($tarifs) }},
                            get filteredTarifs() {
                                if (this.search === '') return this.tarifs;
                                return this.tarifs.filter(t => t.jenis_kendaraan.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            get selectedTarif() {
                                return this.tarifs.find(t => t.jenis_kendaraan === this.jenis) || null;
                            },
                            get selectedLabel() {
                                if (this.selectedTarif) {
                                    return this.selectedTarif.jenis_kendaraan.charAt(0).toUpperCase() + this.selectedTarif.jenis_kendaraan.slice(1) + ' — Rp ' + new Intl.NumberFormat('id-ID').format(this.selectedTarif.tarif_per_jam) + '/jam';
                                }
                                return 'Pilih Jenis Kendaraan';
                            },
                            selectJenis(val) {
                                this.jenis = val;
                                this.search = '';
                                this.open = false;
                            }
                        }" @click.away="open = false">
                            <label class="input-label">Jenis Kendaraan</label>
                            
                            <!-- Searchable Dropdown — dark mode consistent -->
                            <div class="relative">
                                {{-- Trigger button --}}
                                <div @click="open = true"
                                     class="w-full px-4 py-2.5 border rounded-xl cursor-pointer flex justify-between items-center transition-all duration-200
                                            bg-[#020617] text-[#e2e8f0]
                                            @error('jenis_kendaraan') border-rose-500 ring-2 ring-rose-500/20 @else border-[#334155] @enderror
                                            hover:border-cyan-500/50"
                                     :class="{ 'border-cyan-500 ring-2 ring-cyan-500/20': open }">
                                    <span class="text-sm"
                                          x-text="open && search !== '' ? '' : selectedLabel"
                                          :class="{ 'text-[#94a3b8]': !selectedTarif }">
                                    </span>
                                    <svg class="w-4 h-4 text-[#94a3b8] transition-transform duration-200 shrink-0"
                                         :class="{ 'rotate-180': open }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>

                                {{-- Search input (overlays trigger when open) --}}
                                <input x-show="open"
                                       x-model="search"
                                       x-ref="searchInput"
                                       type="text"
                                       placeholder="Cari jenis kendaraan..."
                                       x-on:keydown.escape="open = false"
                                       class="absolute inset-0 w-full px-4 py-2.5 text-sm rounded-xl border-2 border-cyan-500 ring-2 ring-cyan-500/20
                                              bg-[#020617] text-[#e2e8f0] placeholder:text-[#94a3b8]
                                              focus:outline-none transition-all duration-200">

                                {{-- Dropdown panel --}}
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.97]"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute z-50 w-full mt-2 rounded-xl shadow-2xl border border-[#1e293b] overflow-hidden bg-[#0F172A]"
                                     style="display: none;">
                                    <ul class="max-h-60 overflow-auto divide-y divide-[#1e293b]">
                                        <template x-for="t in filteredTarifs" :key="t.id_tarif">
                                            <li @click="selectJenis(t.jenis_kendaraan)"
                                                class="px-4 py-3 cursor-pointer transition-colors duration-150 flex items-center justify-between group"
                                                :class="jenis === t.jenis_kendaraan ? 'bg-cyan-500/10' : 'hover:bg-[#1e293b]'">
                                                <div>
                                                    <p class="text-sm font-semibold capitalize"
                                                       :class="jenis === t.jenis_kendaraan ? 'text-cyan-300' : 'text-[#e2e8f0]'"
                                                       x-text="t.jenis_kendaraan"></p>
                                                    <p class="text-xs text-[#94a3b8] font-medium mt-0.5 font-mono"
                                                       x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(t.tarif_per_jam) + '/jam'"></p>
                                                </div>
                                                <div x-show="jenis === t.jenis_kendaraan" class="text-cyan-400 shrink-0">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </li>
                                        </template>
                                        <template x-if="filteredTarifs.length === 0">
                                            <li class="px-4 py-6 text-center text-[#94a3b8] text-sm">
                                                Tidak ditemukan
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @error('jenis_kendaraan') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>

                        {{-- Area Parkir --}}
                        @php
                            $areaOptions = [];
                            foreach($areas as $area) {
                                $isFull        = $area->isFull();
                                $invalidType   = !$area->canAcceptVehicleType($jenis_kendaraan);
                                $invalidPrivilege = !$area->canAccessByPrivilege($status_spesial);
                                $isDisabled    = $isFull || $invalidType || $invalidPrivilege;
                                $reason        = '';
                                if ($isFull)             $reason = 'PENUH';
                                elseif ($invalidType)    $reason = 'BUKAN UNTUK ' . strtoupper($jenis_kendaraan);
                                elseif ($invalidPrivilege) $reason = 'BUTUH AKSES ' . strtoupper($area->level_akses);

                                $areaOptions[] = [
                                    'id'       => $area->id_area,
                                    'nama'     => $area->nama_area,
                                    'sisa'     => $area->sisa_kapasitas,
                                    'kapasitas'=> $area->kapasitas,
                                    'disabled' => $isDisabled,
                                    'reason'   => $reason,
                                    'pct'      => $area->kapasitas > 0
                                                    ? round(($area->kapasitas - $area->sisa_kapasitas) / $area->kapasitas * 100)
                                                    : 0,
                                ];
                            }
                        @endphp

                        <div x-data="{
                                open: false,
                                selectedId: @entangle('id_area').live,
                                areas: {{ Js::from($areaOptions) }},
                                get selectedArea() {
                                    return this.areas.find(a => String(a.id) === String(this.selectedId)) || null;
                                },
                                get selectedLabel() {
                                    if (this.selectedArea) {
                                        return this.selectedArea.nama + ' (' + this.selectedArea.sisa + '/' + this.selectedArea.kapasitas + ' tersedia)';
                                    }
                                    return 'Pilih Area Parkir';
                                },
                                selectArea(id) {
                                    this.selectedId = id;
                                    this.open = false;
                                }
                            }" @click.away="open = false">

                            <label class="input-label">Area Parkir</label>

                            {{-- Hidden native input to keep wire:model binding --}}
                            <input type="hidden" wire:model="id_area" :value="selectedId">

                            <div class="relative">
                                {{-- Trigger --}}
                                <div @click="open = !open"
                                     class="w-full px-4 py-2.5 border rounded-xl cursor-pointer flex justify-between items-center transition-all duration-200
                                            bg-[#020617] text-[#e2e8f0]
                                            @error('id_area') border-rose-500 ring-2 ring-rose-500/20 @else border-[#334155] @enderror
                                            hover:border-cyan-500/50"
                                     :class="{ 'border-cyan-500 ring-2 ring-cyan-500/20': open }">
                                    <span class="text-sm"
                                          x-text="selectedLabel"
                                          :class="{ 'text-[#94a3b8]': !selectedArea }">
                                    </span>
                                    <svg class="w-4 h-4 text-[#94a3b8] transition-transform duration-200 shrink-0"
                                         :class="{ 'rotate-180': open }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>

                                {{-- Dropdown panel --}}
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.97]"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute z-50 w-full mt-2 rounded-xl shadow-2xl border border-[#1e293b] overflow-hidden bg-[#0F172A]"
                                     style="display: none;">
                                    <ul class="max-h-64 overflow-auto divide-y divide-[#1e293b]">
                                        {{-- Placeholder option --}}
                                        <li @click="selectArea('')"
                                            class="px-4 py-3 cursor-pointer transition-colors duration-150 flex items-center justify-between"
                                            :class="!selectedId ? 'bg-cyan-500/10' : 'hover:bg-[#1e293b]'">
                                            <p class="text-sm font-semibold"
                                               :class="!selectedId ? 'text-cyan-300' : 'text-[#94a3b8]'">
                                                Pilih Area Parkir
                                            </p>
                                        </li>

                                        @foreach($areaOptions as $opt)
                                        <li @click="{{ $opt['disabled'] ? '' : 'selectArea(' . $opt['id'] . ')' }}"
                                            class="px-4 py-3 transition-colors duration-150 flex items-center justify-between
                                                   {{ $opt['disabled'] ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }}"
                                            :class="String(selectedId) === '{{ $opt['id'] }}' ? 'bg-cyan-500/10' : '{{ $opt['disabled'] ? '' : 'hover:bg-[#1e293b]' }}'">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold"
                                                   :class="String(selectedId) === '{{ $opt['id'] }}' ? 'text-cyan-300' : '{{ $opt['disabled'] ? 'text-[#94a3b8]' : 'text-[#e2e8f0]' }}'">
                                                    {{ $opt['nama'] }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-xs font-mono text-[#94a3b8]">
                                                        {{ $opt['sisa'] }}/{{ $opt['kapasitas'] }} tersedia
                                                    </span>
                                                    @if($opt['reason'])
                                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-rose-500/15 text-rose-400 border border-rose-500/25 uppercase tracking-wider">
                                                        {{ $opt['reason'] }}
                                                    </span>
                                                    @endif
                                                </div>
                                                {{-- Mini capacity bar --}}
                                                <div class="mt-1.5 h-1 w-full bg-[#1e293b] rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full transition-all duration-500
                                                                {{ $opt['pct'] >= 100 ? 'bg-rose-500' : ($opt['pct'] >= 70 ? 'bg-amber-400' : 'bg-cyan-500') }}"
                                                         style="width: {{ $opt['pct'] }}%">
                                                    </div>
                                                </div>
                                            </div>
                                            <div x-show="String(selectedId) === '{{ $opt['id'] }}'" class="text-cyan-400 shrink-0 ml-3">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @error('id_area') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="mt-6">
                        <button type="submit"
                                class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-xl
                                       shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:from-emerald-400 hover:to-teal-500
                                       active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2"
                                wire:loading.attr="disabled" wire:loading.class="opacity-75">
                            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <svg wire:loading class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove>Proses Masuk &amp; Cetak Karcis</span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Status Area Parkir ── --}}
        <div>
            <div class="card p-5">
                <h3 class="text-base font-bold text-[#e2e8f0] mb-5 flex items-center gap-2">
                    <span class="w-1.5 h-5 bg-cyan-400 rounded-full"></span>
                    Status Area Parkir
                </h3>
                <div class="space-y-4">
                    @foreach($areas as $area)
                    @php $pct = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas) * 100 : 0; @endphp
                    <div class="p-3 rounded-xl border transition-colors
                        {{ $area->isFull() ? 'bg-rose-500/5 border-rose-500/20' : 'bg-[#020617] border-[#1e293b]' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-[#e2e8f0] text-sm">{{ $area->nama_area }}</span>
                            @if($area->isFull())
                            <span class="badge badge-rose text-[10px]">PENUH</span>
                            @else
                            <span class="text-xs text-cyan-400 font-mono tabular-nums">{{ $area->sisa_kapasitas }} slot</span>
                            @endif
                        </div>
                        <div class="progress-track">
                            <div class="{{ $pct > 80 ? 'progress-fill-full' : ($pct > 50 ? 'progress-fill-warning' : 'progress-fill-ok') }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-[10px] text-[#94a3b8] mt-1.5">{{ $area->terisi }}/{{ $area->kapasitas }} terisi</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
