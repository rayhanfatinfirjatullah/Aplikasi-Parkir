<?php

namespace App\Livewire\Petugas;

use App\Models\Transaksi;
use App\Models\StatusKendaraan;
use App\Models\LogAktivitas;
use App\Models\Pengaturan;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class TransaksiKeluar extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showCheckout = false;
    public ?array $checkoutData = null;

    // Denda karcis hilang
    public bool $isKarcisHilang = false;
    public int $nilaiDenda = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Hitung biaya parkir dengan sistem tarif progresif.
     *
     * Logika:
     *  ≤ grace_period menit   → Rp 0 (gratis / drop-off)
     *  ≤ menit_setengah menit → 50% tarif jam pertama
     *  ≤ 60 menit             → 100% tarif jam pertama
     *  > 60 menit             → tarif jam pertama + ceil(sisa/60) × tarif jam berikutnya
     *
     * @param int   $durasiMenit        Total durasi parkir dalam menit
     * @param float $tarifJamPertama    Tarif jam pertama
     * @param float $tarifJamBerikutnya Tarif setiap jam setelah jam pertama
     * @param int   $gracePeriod        Batas menit gratis (default: 10)
     * @param int   $menitSetengah      Batas menit tarif setengah (default: 30)
     * @return array ['biaya', 'label', 'detail']
     */
    private function hitungBiayaProgresif(
        int $durasiMenit,
        float $tarifJamPertama,
        float $tarifJamBerikutnya,
        int $gracePeriod = 10,
        int $menitSetengah = 30
    ): array {
        if ($durasiMenit <= $gracePeriod) {
            return [
                'biaya'  => 0,
                'label'  => 'Gratis (Grace Period ≤ ' . $gracePeriod . ' menit)',
                'detail' => [],
            ];
        }

        if ($durasiMenit <= $menitSetengah) {
            $biaya = (int) ceil($tarifJamPertama * 0.5);
            return [
                'biaya'  => $biaya,
                'label'  => '50% Tarif Jam Pertama',
                'detail' => [
                    ['desc' => 'Tarif Jam Pertama (50%)', 'nominal' => $biaya],
                ],
            ];
        }

        if ($durasiMenit <= 60) {
            $biaya = (int) $tarifJamPertama;
            return [
                'biaya'  => $biaya,
                'label'  => 'Tarif Jam Pertama (100%)',
                'detail' => [
                    ['desc' => 'Tarif Jam Pertama', 'nominal' => $biaya],
                ],
            ];
        }

        // Lebih dari 60 menit: jam pertama + jam tambahan
        $sisaMenit    = $durasiMenit - 60;
        $jamTambahan  = (int) ceil($sisaMenit / 60);
        $biayaTambahan = $jamTambahan * (int) $tarifJamBerikutnya;
        $biaya        = (int) $tarifJamPertama + $biayaTambahan;

        return [
            'biaya'  => $biaya,
            'label'  => 'Tarif Progresif',
            'detail' => [
                ['desc' => 'Tarif Jam Pertama',                              'nominal' => (int) $tarifJamPertama],
                ['desc' => $jamTambahan . ' Jam Berikutnya × Rp ' . number_format((int) $tarifJamBerikutnya, 0, ',', '.'), 'nominal' => $biayaTambahan],
            ],
        ];
    }

    /**
     * Hitung biaya berdasarkan metode tarif status kendaraan.
     * Fallback ke hitungBiayaProgresif() untuk semua tarif reguler.
     */
    private function resolvebiaya(
        Transaksi $transaksi,
        string $metodeTarif,
        float $nominalTarif,
        int $durasiMenit,
        int $gracePeriod,
        int $menitSetengah
    ): array {
        if ($metodeTarif === 'fix_per_hari') {
            $hasPaidToday = Transaksi::where('plat_nomor', $transaksi->plat_nomor)
                ->where('status', 'keluar')
                ->whereDate('waktu_keluar', today())
                ->where('biaya_parkir', '>', 0)
                ->exists();
            $biaya = $hasPaidToday ? 0 : (int) $nominalTarif;
            return [
                'biaya'  => $biaya,
                'label'  => $hasPaidToday ? 'Sudah Bayar Hari Ini (Rp 0)' : 'Tarif Harian',
                'detail' => [['desc' => 'Tarif Harian', 'nominal' => $biaya]],
            ];
        }

        if ($metodeTarif === 'membership') {
            if ($transaksi->kendaraan && $transaksi->kendaraan->isMembershipActive()) {
                return [
                    'biaya'  => 0,
                    'label'  => 'Membership Aktif (Gratis)',
                    'detail' => [],
                ];
            }
            // Membership expired → fallback ke tarif reguler
            return $this->hitungBiayaProgresif(
                $durasiMenit,
                (float) $transaksi->tarif->tarif_jam_pertama,
                (float) $transaksi->tarif->tarif_jam_berikutnya,
                $gracePeriod,
                $menitSetengah
            );
        }

        // Default: reguler — tarif progresif
        return $this->hitungBiayaProgresif(
            $durasiMenit,
            (float) $transaksi->tarif->tarif_jam_pertama,
            (float) $transaksi->tarif->tarif_jam_berikutnya,
            $gracePeriod,
            $menitSetengah
        );
    }

    public function openCheckout($id)
    {
        $this->isKarcisHilang = false;
        $this->nilaiDenda     = Pengaturan::getDendaKarcisHilang();

        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'areaParkir'])->findOrFail($id);

        // Ambil parameter dari database
        $gracePeriod   = Pengaturan::getGracePeriod();
        $menitSetengah = Pengaturan::getMenitTarifSetengah();

        $waktuKeluar = now();
        $waktuMasuk  = Carbon::parse($transaksi->waktu_masuk);

        // Hitung durasi akurat hingga detik, konversi ke menit (floor)
        $durasiDetik = $waktuMasuk->diffInSeconds($waktuKeluar);
        $durasiMenit = (int) floor($durasiDetik / 60);
        $durasiJam   = (int) floor($durasiMenit / 60);
        $sisaMenit   = $durasiMenit % 60;

        $statusSpesial  = $transaksi->status_spesial ?? 'reguler';
        $statusModel    = StatusKendaraan::where('nama_status', $statusSpesial)->first();
        $metodeTarif    = $statusModel ? $statusModel->metode_tarif : 'reguler';
        $nominalTarif   = $statusModel ? (float) $statusModel->nominal_tarif : 0;
        $isImmuneDenda  = $statusModel ? $statusModel->is_bebas_denda : false;
        $isMembershipExpired = false;

        // Deteksi membership expired untuk notifikasi UI
        if ($metodeTarif === 'membership') {
            if (!($transaksi->kendaraan && $transaksi->kendaraan->isMembershipActive())) {
                $isMembershipExpired = true;
                $this->dispatch('toast', type: 'error', message: 'Membership Kadaluwarsa — Tarif Reguler Diterapkan');
            }
        }

        $hasil = $this->resolvebiaya(
            $transaksi, $metodeTarif, $nominalTarif,
            $durasiMenit, $gracePeriod, $menitSetengah
        );

        $biayaParkir = $hasil['biaya'];

        $this->checkoutData = [
            'id_parkir'            => $transaksi->id_parkir,
            'plat_nomor'           => $transaksi->plat_nomor,
            'warna'                => $transaksi->warna,
            'status_spesial'       => $statusSpesial,
            'metode_tarif'         => $metodeTarif,
            'is_membership_expired'=> $isMembershipExpired,
            'is_immune_denda'      => $isImmuneDenda,
            'pemilik'              => $transaksi->pemilik,
            'jenis_kendaraan'      => ucfirst($transaksi->tarif->jenis_kendaraan),
            'area'                 => $transaksi->areaParkir->nama_area,
            'waktu_masuk'          => $transaksi->waktu_masuk->format('d/m/Y H:i'),
            'waktu_keluar'         => $waktuKeluar->format('d/m/Y H:i'),
            // Durasi display
            'durasi_menit'         => $durasiMenit,
            'durasi_jam'           => $durasiJam,
            'durasi_sisa_menit'    => $sisaMenit,
            'durasi_label'         => ($durasiJam > 0 ? $durasiJam . ' Jam ' : '') . $sisaMenit . ' Menit',
            // Tarif info
            'tarif_jam_pertama'    => (int) $transaksi->tarif->tarif_jam_pertama,
            'tarif_jam_berikutnya' => (int) $transaksi->tarif->tarif_jam_berikutnya,
            'grace_period'         => $gracePeriod,
            'menit_setengah'       => $menitSetengah,
            // Biaya
            'biaya_parkir'         => $biayaParkir,
            'label_tarif'          => $hasil['label'],
            'rincian_biaya'        => $hasil['detail'],
            'denda'                => 0,
            'biaya_total'          => $biayaParkir,
        ];

        $this->showCheckout = true;
    }

    /**
     * Reactively recalculate total when checkbox changes.
     */
    public function updatedIsKarcisHilang()
    {
        if (!$this->checkoutData) return;

        $isImmune = $this->checkoutData['is_immune_denda'] ?? false;
        $denda    = ($this->isKarcisHilang && !$isImmune) ? $this->nilaiDenda : 0;

        $this->checkoutData['denda']       = $denda;
        $this->checkoutData['biaya_total'] = $this->checkoutData['biaya_parkir'] + $denda;
    }

    public function checkout()
    {
        if (!$this->checkoutData) return;

        $transaksi = Transaksi::with(['areaParkir', 'tarif', 'kendaraan'])->findOrFail($this->checkoutData['id_parkir']);

        // Ambil parameter dari database (recalculate saat confirm — anti-manipulasi)
        $gracePeriod   = Pengaturan::getGracePeriod();
        $menitSetengah = Pengaturan::getMenitTarifSetengah();

        $waktuKeluar = now();
        $waktuMasuk  = Carbon::parse($transaksi->waktu_masuk);

        $durasiDetik = $waktuMasuk->diffInSeconds($waktuKeluar);
        $durasiMenit = (int) floor($durasiDetik / 60);
        // Simpan durasi_jam = total menit / 60 (ceil) untuk backward compat laporan
        $durasiJamDb = (int) ceil($durasiMenit > 0 ? $durasiMenit / 60 : 1);

        $statusSpesial = $transaksi->status_spesial ?? 'reguler';
        $statusModel   = StatusKendaraan::where('nama_status', $statusSpesial)->first();
        $metodeTarif   = $statusModel ? $statusModel->metode_tarif : 'reguler';
        $nominalTarif  = $statusModel ? (float) $statusModel->nominal_tarif : 0;
        $isImmuneDenda = $statusModel ? $statusModel->is_bebas_denda : false;

        $hasil       = $this->resolvebiaya($transaksi, $metodeTarif, $nominalTarif, $durasiMenit, $gracePeriod, $menitSetengah);
        $biayaParkir = $hasil['biaya'];

        $denda      = ($this->isKarcisHilang && !$isImmuneDenda) ? $this->nilaiDenda : 0;
        $biayaTotal = $biayaParkir + $denda;

        $transaksi->update([
            'waktu_keluar' => $waktuKeluar,
            'durasi_jam'   => $durasiJamDb,
            'biaya_parkir' => $biayaParkir,
            'biaya_total'  => $biayaTotal,
            'denda'        => $denda,
            'status'       => 'keluar',
        ]);

        // Decrement slot area terisi
        $transaksi->areaParkir->decrement('terisi');

        // Log aktivitas
        $durasiJam      = (int) floor($durasiMenit / 60);
        $durasiSisaMnt  = $durasiMenit % 60;
        $durasiText     = ($durasiJam > 0 ? $durasiJam . ' jam ' : '') . $durasiSisaMnt . ' menit';
        $logMessage     = "Check-out kendaraan {$transaksi->plat_nomor}, durasi: {$durasiText}, biaya: Rp " . number_format($biayaTotal, 0, ',', '.');
        if ($denda > 0) {
            $logMessage .= " (termasuk denda karcis hilang: Rp " . number_format($denda, 0, ',', '.') . ")";
        }

        LogAktivitas::create([
            'id_user'        => auth()->user()->id_user,
            'aktivitas'      => $logMessage,
            'waktu_aktivitas'=> now(),
        ]);

        $this->showCheckout   = false;
        $this->isKarcisHilang = false;

        return redirect()->route('petugas.cetak-struk', $transaksi->id_parkir);
    }

    public function render()
    {
        $transaksis = Transaksi::with(['tarif', 'areaParkir', 'user'])
            ->where('status', 'masuk')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('plat_nomor', 'like', "%{$this->search}%")
                       ->orWhere('id_parkir', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('waktu_masuk', 'desc')
            ->paginate(10);

        $statuses = StatusKendaraan::all();
        return view('livewire.petugas.transaksi-keluar', compact('transaksis', 'statuses'))
            ->layout('layouts.app');
    }
}
