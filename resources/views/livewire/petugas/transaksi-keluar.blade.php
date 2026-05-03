@section('page-title', 'Kendaraan Keluar')
@section('page-subtitle', 'Proses kendaraan keluar dan perhitungan biaya')

<div>
    {{-- Search Bar --}}
    <div class="mb-6">
        <div class="relative max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari plat nomor atau no. karcis..."
                   class="input-base pl-10 w-full sm:w-80">
        </div>
    </div>

    {{-- ── Active Parking Table ── --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-[#1e293b] flex items-center gap-2">
            <span class="w-1.5 h-5 bg-amber-400 rounded-full"></span>
            <h3 class="text-base font-bold text-[#e2e8f0]">Kendaraan Sedang Terparkir</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No. Karcis</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Area</th>
                        <th>Waktu Masuk</th>
                        <th>Durasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                    <tr wire:key="trx-{{ $t->id_parkir }}">
                        <td class="font-mono tabular-nums text-[#94a3b8] text-xs">#{{ str_pad($t->id_parkir, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold tracking-widest text-[#e2e8f0]">{{ $t->plat_nomor }}</span>
                                @php
                                    $statusObj = collect($statuses)->firstWhere('nama_status', $t->status_spesial ?? 'reguler');
                                @endphp
                                @if($statusObj && $statusObj->nama_status !== 'reguler')
                                <span class="badge badge-{{ $statusObj->warna_badge }} text-[10px]">{{ $statusObj->label_status }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-[#94a3b8]">{{ ucfirst($t->tarif->jenis_kendaraan) }}</td>
                        <td class="text-[#94a3b8]">{{ $t->areaParkir->nama_area }}</td>
                        <td class="font-mono tabular-nums text-xs text-[#94a3b8]">{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                        <td>
                            @php
                                $mnt  = (int) floor($t->waktu_masuk->diffInSeconds(now()) / 60);
                                $jam  = (int) floor($mnt / 60);
                                $sisa = $mnt % 60;
                            @endphp
                            <span class="badge badge-amber">
                                {{ $jam > 0 ? $jam . 'j ' : '' }}{{ $sisa }}m
                            </span>
                        </td>
                        <td class="text-center">
                            <button wire:click="openCheckout({{ $t->id_parkir }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-2
                                           bg-gradient-to-r from-rose-600 to-orange-500 text-white text-xs font-bold rounded-lg
                                           shadow-lg shadow-rose-500/20 hover:shadow-rose-500/40 hover:from-rose-500 hover:to-orange-400
                                           active:scale-95 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                                </svg>
                                Proses Keluar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-[#94a3b8]">
                            <svg class="w-10 h-10 mx-auto mb-3 text-[#334155]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1.293-1.293A1 1 0 015 14.414V13H4v2H2v2h2l1-1h8l1 1h2v-2h-1M13 16V6l5 3v7h-5z"/>
                            </svg>
                            Tidak ada kendaraan yang sedang terparkir
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[#1e293b]">{{ $transaksis->links('vendor.livewire.custom-pagination') }}</div>
    </div>

    {{-- ── Checkout Modal ── --}}
    @if($showCheckout && $checkoutData)
    <div class="modal-backdrop">
        <div class="modal-box p-6 max-w-lg">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#e2e8f0]">Konfirmasi Check-out</h3>
                <button wire:click="$set('showCheckout', false)"
                        class="p-2 text-[#94a3b8] hover:text-[#e2e8f0] hover:bg-[#1e293b] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-0 mb-5">

                {{-- ── Data Kendaraan ── --}}
                <div class="flex justify-between items-center py-2.5 border-b border-[#1e293b]">
                    <span class="text-sm text-[#94a3b8]">Plat Nomor</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-[#e2e8f0] tracking-wider">{{ $checkoutData['plat_nomor'] }}</span>
                        @php
                            $statusObjCheckout = collect($statuses)->firstWhere('nama_status', $checkoutData['status_spesial'] ?? 'reguler');
                        @endphp
                        @if($statusObjCheckout && $statusObjCheckout->nama_status !== 'reguler')
                        <span class="badge badge-{{ $statusObjCheckout->warna_badge }} text-[10px]">{{ $statusObjCheckout->label_status }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between py-2.5 border-b border-[#1e293b]">
                    <span class="text-sm text-[#94a3b8]">Jenis Kendaraan</span>
                    <span class="text-sm text-[#e2e8f0]">{{ $checkoutData['jenis_kendaraan'] }}</span>
                </div>
                <div class="flex justify-between py-2.5 border-b border-[#1e293b]">
                    <span class="text-sm text-[#94a3b8]">Area Parkir</span>
                    <span class="text-sm text-[#e2e8f0]">{{ $checkoutData['area'] }}</span>
                </div>
                <div class="flex justify-between py-2.5 border-b border-[#1e293b]">
                    <span class="text-sm text-[#94a3b8]">Waktu Masuk</span>
                    <span class="text-sm font-mono text-[#e2e8f0]">{{ $checkoutData['waktu_masuk'] }}</span>
                </div>
                <div class="flex justify-between py-2.5 border-b border-[#1e293b]">
                    <span class="text-sm text-[#94a3b8]">Waktu Keluar</span>
                    <span class="text-sm font-mono text-[#e2e8f0]">{{ $checkoutData['waktu_keluar'] }}</span>
                </div>

                {{-- ── Durasi ── --}}
                <div class="flex justify-between py-2.5 border-b border-[#1e293b]">
                    <span class="text-sm text-[#94a3b8]">Durasi Parkir</span>
                    <span class="text-sm font-bold text-amber-400">
                        {{ $checkoutData['durasi_label'] }}
                    </span>
                </div>

                {{-- ── Tarif Info ── --}}
                @if($checkoutData['metode_tarif'] === 'reguler' || $checkoutData['is_membership_expired'])
                <div class="py-2.5 border-b border-[#1e293b]">
                    <div class="flex justify-between items-start">
                        <span class="text-sm text-[#94a3b8]">Skema Tarif</span>
                        <div class="text-right space-y-0.5">
                            <p class="text-xs text-[#94a3b8]">
                                Jam Pertama: <span class="text-cyan-400 font-semibold">Rp {{ number_format($checkoutData['tarif_jam_pertama'], 0, ',', '.') }}</span>
                            </p>
                            <p class="text-xs text-[#94a3b8]">
                                Jam Berikutnya: <span class="text-cyan-400 font-semibold">Rp {{ number_format($checkoutData['tarif_jam_berikutnya'], 0, ',', '.') }}/jam</span>
                            </p>
                            <p class="text-xs text-[#64748b]">
                                Grace Period: {{ $checkoutData['grace_period'] }} menit &middot; ½ jam s/d: {{ $checkoutData['menit_setengah'] }} menit
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ── Biaya Parkir ── --}}
                <div class="flex justify-between items-start py-2.5 border-b border-[#1e293b]">
                    <div>
                        <span class="text-sm text-[#94a3b8]">Biaya Parkir</span>
                        @if($checkoutData['is_membership_expired'] ?? false)
                        <p class="text-xs font-bold text-rose-400 mt-0.5">Membership Expired — Tarif Reguler</p>
                        @endif
                    </div>
                    @if(isset($checkoutData['metode_tarif']) && $checkoutData['metode_tarif'] !== 'reguler' && !$checkoutData['is_membership_expired'] && $checkoutData['biaya_parkir'] == 0)
                        <span class="text-sm font-bold text-emerald-400">BEBAS BIAYA (Rp 0)</span>
                    @elseif($checkoutData['biaya_parkir'] == 0 && $checkoutData['durasi_menit'] <= $checkoutData['grace_period'])
                        <span class="text-sm font-bold text-emerald-400">GRATIS — Drop-off (Rp 0)</span>
                    @else
                        <span class="text-sm text-[#e2e8f0] tabular-nums font-semibold">Rp {{ number_format($checkoutData['biaya_parkir'], 0, ',', '.') }}</span>
                    @endif
                </div>

                {{-- ── Rincian Perhitungan ── --}}
                @if(!empty($checkoutData['rincian_biaya']))
                <div class="py-2 border-b border-[#1e293b]">
                    <p class="text-[10px] font-bold text-[#64748b] uppercase tracking-widest mb-1.5">Rincian Perhitungan</p>
                    @foreach($checkoutData['rincian_biaya'] as $item)
                    <div class="flex justify-between items-center py-0.5">
                        <span class="text-xs text-[#64748b]">{{ $item['desc'] }}</span>
                        <span class="text-xs font-mono text-[#94a3b8]">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- ── Label Tarif ── --}}
                @if($checkoutData['label_tarif'])
                <div class="flex justify-between items-center py-2 border-b border-[#1e293b]">
                    <span class="text-[10px] text-[#64748b] uppercase tracking-wider">Skema Aktif</span>
                    <span class="text-xs font-semibold px-2 py-0.5 bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 rounded-full">
                        {{ $checkoutData['label_tarif'] }}
                    </span>
                </div>
                @endif

                {{-- ── Denda ── --}}
                @if($checkoutData['is_immune_denda'] ?? false)
                <div class="py-2.5 border-b border-[#1e293b]">
                    <div class="flex items-center gap-2 p-2.5 bg-amber-500/10 rounded-xl border border-amber-500/20">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-medium text-amber-400">Kebal Denda Karcis Hilang (Hak Spesial)</span>
                    </div>
                </div>
                @else
                <div class="py-2.5 border-b border-[#1e293b]">
                    <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-xl hover:bg-[#1e293b] transition-colors">
                        <input type="checkbox" wire:model.live="isKarcisHilang"
                               class="w-5 h-5 rounded border-[#334155] bg-[#020617] text-rose-500 focus:ring-rose-500/30">
                        <div>
                            <span class="text-sm font-medium text-[#e2e8f0] group-hover:text-rose-400 transition-colors">Karcis Hilang?</span>
                            <span class="text-xs text-[#94a3b8] ml-1">(Denda Rp {{ number_format($nilaiDenda, 0, ',', '.') }})</span>
                        </div>
                    </label>
                </div>
                @if($isKarcisHilang)
                <div class="flex justify-between py-2.5 border-b border-[#1e293b]">
                    <span class="text-sm font-semibold text-rose-400">Denda Karcis Hilang</span>
                    <span class="text-sm font-bold text-rose-400 tabular-nums animate-pulse">
                        Rp {{ number_format($checkoutData['denda'], 0, ',', '.') }}
                    </span>
                </div>
                @endif
                @endif

                {{-- ── Total ── --}}
                <div class="flex justify-between items-center py-4 px-5 mt-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                    <span class="font-bold text-emerald-400 text-base">Total Biaya</span>
                    <span class="font-extrabold text-emerald-400 text-xl tabular-nums">
                        Rp {{ number_format($checkoutData['biaya_total'], 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button wire:click="$set('showCheckout', false)" class="btn-secondary">Batal</button>
                <button wire:click="checkout"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                               bg-gradient-to-r from-emerald-500 to-teal-600
                               shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40
                               active:scale-[0.97] transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                    </svg>
                    Konfirmasi &amp; Cetak Struk
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
