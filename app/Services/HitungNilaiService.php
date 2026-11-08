<?php

namespace App\Services;

use App\Models\ConfigBobot;
use App\Models\Nilai;

class HitungNilaiService
{
    /**
     * Hitung nilai akhir berdasarkan bobot yang aktif.
     * Rumus:
     *   nilai_dudi   = rata-rata(nilai_kinerja, nilai_kompetensi)
     *   nilai_guru   = rata-rata(nilai_laporan, nilai_presentasi)
     *   nilai_sidang = nilai_sidang
     *   nilai_akhir  = (nilai_dudi * bobot_dudi/100)
     *                + (nilai_guru * bobot_guru/100)
     *                + (nilai_sidang * bobot_sidang/100)
     */
    public static function hitung(Nilai $nilai): Nilai
    {
        $bobot = ConfigBobot::getActive();

        $nilaiDudi   = self::rataRata([$nilai->nilai_kinerja, $nilai->nilai_kompetensi]);
        $nilaiGuru   = self::rataRata([$nilai->nilai_laporan, $nilai->nilai_presentasi]);
        $nilaiSidang = $nilai->nilai_sidang !== null ? (float) $nilai->nilai_sidang : null;

        $nilaiAkhir = self::hitungAkhir(
            $nilaiDudi,
            $nilaiGuru,
            $nilaiSidang,
            (float) $bobot->bobot_dudi,
            (float) $bobot->bobot_guru,
            (float) $bobot->bobot_sidang
        );

        $nilai->nilai_akhir = $nilaiAkhir;
        $nilai->save();

        return $nilai;
    }

    /**
     * Hitung nilai akhir dari 3 komponen.
     * Kalau salah satu komponen null, komponen itu dianggap tidak ada
     * dan bobotnya dinormalisasi ulang dari komponen yang ada.
     */
    public static function hitungAkhir(
        ?float $nilaiDudi,
        ?float $nilaiGuru,
        ?float $nilaiSidang,
        float $bobotDudi,
        float $bobotGuru,
        float $bobotSidang
    ): ?float {
        $totalBobot = 0;
        $totalNilai = 0;

        if ($nilaiDudi !== null) {
            $totalBobot += $bobotDudi;
            $totalNilai += $nilaiDudi * $bobotDudi;
        }

        if ($nilaiGuru !== null) {
            $totalBobot += $bobotGuru;
            $totalNilai += $nilaiGuru * $bobotGuru;
        }

        if ($nilaiSidang !== null) {
            $totalBobot += $bobotSidang;
            $totalNilai += $nilaiSidang * $bobotSidang;
        }

        if ($totalBobot <= 0) {
            return null;
        }

        return round($totalNilai / $totalBobot, 2);
    }

    /**
     * Rata-rata dari array nilai. Kalau semua null, return null.
     */
    public static function rataRata(array $values): ?float
    {
        $valid = array_filter($values, fn ($v) => $v !== null);

        if (empty($valid)) {
            return null;
        }

        return round(array_sum($valid) / count($valid), 2);
    }

    /**
     * Ambil predikat dari nilai akhir.
     */
    public static function predikat(?float $nilaiAkhir): string
    {
        if ($nilaiAkhir === null) {
            return '-';
        }

        return match (true) {
            $nilaiAkhir >= 90 => 'A',
            $nilaiAkhir >= 80 => 'B',
            $nilaiAkhir >= 70 => 'C',
            $nilaiAkhir >= 60 => 'D',
            default            => 'E',
        };
    }
}