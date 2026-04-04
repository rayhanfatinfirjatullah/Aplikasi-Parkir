@section('page-title', 'Kendaraan Masuk')
@section('page-subtitle', 'Catat kendaraan masuk area parkir')

<div>
    @if($showSuccess && $lastTransaction)
    <!-- Success Message -->
    <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-emerald-800 dark:text-emerald-300">Kendaraan Berhasil Masuk!</h3>
                <div class="mt-2 grid grid-cols-2 gap-2 text-sm text-emerald-700 dark:text-emerald-400">
                    <p><span class="font-medium">Plat Nomor:</span> {{ $lastTransaction['plat_nomor'] }}</p>
                    <p><span class="font-medium">Jenis:</span> {{ $lastTransaction['jenis_kendaraan'] }}</p>
                    <p><span class="font-medium">Area:</span> {{ $lastTransaction['area'] }}</p>
                    <p><span class="font-medium">Waktu Masuk:</span> {{ $lastTransaction['waktu_masuk'] }}</p>
                </div>
                <button wire:click="resetForm" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Check-in -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Form Check-in Kendaraan</h3>
                <form wire:submit="checkin">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Plat Nomor</label>
                            <input wire:model.blur="plat_nomor" type="text" placeholder="Contoh: B 1234 ABC" x-on:input="$el.value = $el.value.toUpperCase()" maxlength="11"
                                   class="w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 text-lg font-bold tracking-wider uppercase">
                            @error('plat_nomor') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Warna Kendaraan</label>
                            <input wire:model="warna" type="text" placeholder="Contoh: Hitam"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            @error('warna') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pemilik</label>
                            <input wire:model="pemilik" type="text" placeholder="Nama pemilik kendaraan"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            @error('pemilik') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis Kendaraan</label>
                            <select wire:model="jenis_kendaraan"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                                @foreach($tarifs as $tarif)
                                <option value="{{ $tarif->jenis_kendaraan }}">{{ ucfirst($tarif->jenis_kendaraan) }} - Rp {{ number_format($tarif->tarif_per_jam, 0, ',', '.') }}/jam</option>
                                @endforeach
                            </select>
                            @error('jenis_kendaraan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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
                            @error('id_area') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300 flex items-center justify-center gap-2"
                                wire:loading.attr="disabled" wire:loading.class="opacity-75">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span wire:loading.remove>Proses Masuk</span>
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
