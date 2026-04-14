@section('page-title', 'Kendaraan Masuk')
@section('page-subtitle', 'Catat kendaraan masuk area parkir')

<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Check-in -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Form Check-in Kendaraan
                </h3>

                <form wire:submit="checkin">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2 relative" x-data="{
                            open: false,
                            search: @entangle('plat_nomor'),
                            vehicles: {{ Js::from($registeredVehicles) }},
                            get filteredVehicles() {
                                if (!this.search || this.search.length < 1) return [];
                                let s = this.search.toString().replace(/[^A-Z0-9]/gi, '').toLowerCase();
                                return this.vehicles.filter(v => v.plat_nomor.replace(/[^A-Z0-9]/gi, '').toLowerCase().includes(s)).slice(0, 5); // display top 5 matching
                            },
                            selectVehicle(vehicle) {
                                this.search = vehicle.plat_nomor;
                                this.open = false;
                                // Auto-fill triggers through Livewire
                                $wire.fillVehicleData();
                            },
                            formatPlat(val) {
                                let clean = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
                                let res = '';
                                let i = 0;
                                let p1 = '';
                                while (i < clean.length && p1.length < 2 && /[A-Z]/.test(clean[i])) { p1 += clean[i++]; }
                                res += p1;
                                
                                let p2 = '';
                                while (i < clean.length && p2.length < 4 && /[0-9]/.test(clean[i])) { p2 += clean[i++]; }
                                if (p2.length > 0) res += (res.length > 0 ? ' ' : '') + p2;
                                
                                let p3 = '';
                                while (i < clean.length && p3.length < 3 && /[A-Z]/.test(clean[i])) { p3 += clean[i++]; }
                                if (p3.length > 0) res += (res.length > 0 ? ' ' : '') + p3;
                                
                                return res;
                            }
                        }" @click.away="open = false">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Plat Nomor</label>
                            <div class="relative">
                                <!-- Trigger input -->
                                <input x-model="search" x-on:input="search = formatPlat($event.target.value); open = true" x-on:focus="open = true" @keydown.escape.window="open = false" type="text" placeholder="Contoh: B 1234 ABC" maxlength="11" autocomplete="off" class="w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 text-lg font-bold tracking-wider uppercase @error('plat_nomor') border-red-500 @enderror">
                                
                                <!-- Search Icon -->
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>

                                <!-- Custom Dropdown List -->
                                <div x-show="open && filteredVehicles.length > 0" x-transition.opacity class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden" style="display: none;">
                                    <ul class="max-h-60 overflow-auto divide-y divide-slate-100 dark:divide-slate-700">
                                        <template x-for="v in filteredVehicles" :key="v.id_kendaraan">
                                            <li @click="selectVehicle(v)" class="p-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors flex justify-between items-center group">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-800/50 group-hover:bg-blue-100 dark:group-hover:bg-blue-800/50 transition-colors">
                                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-slate-800 dark:text-white tracking-wider" x-text="v.plat_nomor"></h4>
                                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium" x-text="v.warna + ' - ' + v.pemilik"></p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Badges VIP/VVIP -->
                                                <div>
                                                    <template x-if="v.status_spesial === 'vvip'">
                                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 rounded-full text-[10px] font-bold uppercase tracking-wider border border-amber-200 dark:border-amber-800">VVIP</span>
                                                    </template>
                                                    <template x-if="v.status_spesial === 'vip'">
                                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 rounded-full text-[10px] font-bold uppercase tracking-wider border border-blue-200 dark:border-blue-800">VIP</span>
                                                    </template>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @error('plat_nomor')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Warna Kendaraan</label>
                            <input wire:model="warna" type="text" placeholder="Contoh: Hitam" x-data x-on:input="$el.value = $el.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('warna') border-red-500 @enderror">
                            @error('warna')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pemilik</label>
                            <input wire:model="pemilik" type="text" placeholder="Nama pemilik kendaraan" x-data x-on:input="$el.value = $el.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('pemilik') border-red-500 @enderror">
                            @error('pemilik')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis Kendaraan</label>
                            <select wire:model.live="jenis_kendaraan"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('jenis_kendaraan') border-red-500 @enderror">
                                @foreach($tarifs as $tarif)
                                <option value="{{ $tarif->jenis_kendaraan }}">{{ ucfirst($tarif->jenis_kendaraan) }} - Rp {{ number_format($tarif->tarif_per_jam, 0, ',', '.') }}/jam</option>
                                @endforeach
                            </select>
                            @error('jenis_kendaraan')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Area Parkir</label>
                            <select wire:model="id_area"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 @error('id_area') border-red-500 @enderror">
                                <option value="">Pilih Area</option>
                                @foreach($areas as $area)
                                @php
                                    $isFull = $area->isFull();
                                    $invalidType = !$area->canAcceptVehicleType($jenis_kendaraan);
                                    $invalidPrivilege = !$area->canAccessByPrivilege($status_spesial);
                                    $isDisabled = $isFull || $invalidType || $invalidPrivilege;
                                    $reason = '';
                                    if ($isFull) $reason = 'PENUH';
                                    elseif ($invalidType) $reason = 'BUKAN UNTUK ' . strtoupper($jenis_kendaraan);
                                    elseif ($invalidPrivilege) $reason = 'BUTUH AKSES ' . strtoupper($area->level_akses);
                                @endphp
                                <option value="{{ $area->id_area }}" {{ $isDisabled ? 'disabled' : '' }} class="{{ $isDisabled ? 'text-red-500' : '' }}">
                                    {{ $area->nama_area }} ({{ $area->sisa_kapasitas }}/{{ $area->kapasitas }} tersedia) {{ $reason ? '- ' . $reason : '' }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_area')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300 flex items-center justify-center gap-2"
                                wire:loading.attr="disabled" wire:loading.class="opacity-75">
                            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <svg wire:loading class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove>Proses Masuk & Cetak Karcis</span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Status Area Parkir -->
        <div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Status Area Parkir</h3>
                <div class="space-y-4">
                    @foreach($areas as $area)
                    <div class="p-3 rounded-xl {{ $area->isFull() ? 'bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800' : 'bg-slate-50 dark:bg-slate-700/50' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-slate-800 dark:text-white text-sm">{{ $area->nama_area }}</span>
                            @if($area->isFull())
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full text-xs font-medium">PENUH</span>
                            @else
                            <span class="text-xs text-slate-500">{{ $area->sisa_kapasitas }} tersedia</span>
                            @endif
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                            @php $pct = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas) * 100 : 0; @endphp
                            <div class="h-2 rounded-full {{ $pct > 80 ? 'bg-red-500' : ($pct > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">{{ $area->terisi }}/{{ $area->kapasitas }} terisi</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
