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

                @if ($errors->any())
                <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <form wire:submit="checkin">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2" x-data="{
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
                            <input wire:model.blur="plat_nomor" type="text" placeholder="Contoh: B 1234 ABC"
                                   maxlength="11"
                                   x-on:input="formatPlat($el)"
                                   oninput="this.value = this.value.toUpperCase()"
                                   class="w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 text-lg font-bold tracking-wider uppercase">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Warna Kendaraan</label>
                            <input wire:model="warna" type="text" placeholder="Contoh: Hitam" x-data x-on:input="$el.value = $el.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pemilik</label>
                            <input wire:model="pemilik" type="text" placeholder="Nama pemilik kendaraan" x-data x-on:input="$el.value = $el.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis Kendaraan</label>
                            <select wire:model="jenis_kendaraan"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                                @foreach($tarifs as $tarif)
                                <option value="{{ $tarif->jenis_kendaraan }}">{{ ucfirst($tarif->jenis_kendaraan) }} - Rp {{ number_format($tarif->tarif_per_jam, 0, ',', '.') }}/jam</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Area Parkir</label>
                            <select wire:model="id_area"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Area</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id_area }}" {{ $area->isFull() ? 'disabled' : '' }}>
                                    {{ $area->nama_area }} ({{ $area->sisa_kapasitas }}/{{ $area->kapasitas }} tersedia) {{ $area->isFull() ? '- PENUH' : '' }}
                                </option>
                                @endforeach
                            </select>
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
