<?php

namespace App\Services;

use App\Models\Nilai;
use App\Models\PenempatanPkl;
use App\Models\PengaturanSertifikat;
use App\Models\Sertifikat;
use App\Models\SertifikatTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;

class SertifikatService
{
    /**
     * Generate nomor sertifikat otomatis dengan format dari CMS.
     * Placeholder yang didukung: {no}, {tahun}, {bulan}
     */
    public function generateNomorSertifikat(): string
    {
        $setting = PengaturanSertifikat::getActive();
        $format = $setting->nomor_format ?: '{no}/PKL/SMKS-MST/{tahun}';

        $tahun = date('Y');
        $lastNumber = Sertifikat::whereYear('created_at', $tahun)->count() + 1;

        do {
            $no = str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
            $nomor = str_replace(
                ['{no}', '{tahun}', '{bulan}'],
                [$no, $tahun, date('m')],
                $format
            );
            $exists = Sertifikat::where('nomor_sertifikat', $nomor)->exists();
            if ($exists) {
                $lastNumber++;
            }
        } while ($exists);

        return $nomor;
    }

    /**
     * Buat sertifikat draft untuk sebuah penempatan.
     */
    public function buatDraft(PenempatanPkl $penempatan): Sertifikat
    {
        $existing = Sertifikat::where('penempatan_pkl_id', $penempatan->id)->first();
        if ($existing) {
            return $existing;
        }

        return Sertifikat::create([
            'penempatan_pkl_id' => $penempatan->id,
            'siswa_id' => $penempatan->siswa_id,
            'nomor_sertifikat' => $this->generateNomorSertifikat(),
            'qr_token' => Str::random(40),
            'status' => Sertifikat::STATUS_DRAFT,
        ]);
    }

    /**
     * Generate QR code sebagai data URI (SVG base64).
     */
    public function generateQrDataUri(string $url, int $size = 200): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($url);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Convert gambar upload jadi base64 data URI (biar dompdf bisa baca).
     */
    public function fileToDataUri(?string $relativePath): ?string
    {
        if (! $relativePath) {
            return null;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $relativePath), '/');

        $fullPath = storage_path('app/public/' . $cleanPath);
        if (! file_exists($fullPath)) {
            $fullPath = public_path('storage/' . $cleanPath);
            if (! file_exists($fullPath)) {
                return null;
            }
        }

        $mime = mime_content_type($fullPath) ?: 'image/png';
        $content = file_get_contents($fullPath);

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    /**
     * Render PDF sertifikat.
     * Kalau ada template upload per jurusan -> pakai template itu.
     * Kalau gak ada -> fallback ke blade default.
     */
    public function renderPdf(Sertifikat $sertifikat): string
    {
        $sertifikat->load([
            'siswa.jurusan',
            'penempatanPkl.dudi',
            'penempatanPkl.guruPembimbing',
            'penempatanPkl.guruPenguji',
        ]);

        $setting = PengaturanSertifikat::getActive();
        $nilai = Nilai::where('penempatan_pkl_id', $sertifikat->penempatan_pkl_id)->first();

        $verifyUrl = url('/verifikasi/' . $sertifikat->qr_token);
        $qrDataUri = $this->generateQrDataUri($verifyUrl, 150);

        $jurusanId = $sertifikat->siswa?->jurusan_id;
        $template = SertifikatTemplate::getForJurusan($jurusanId);

        if ($template) {
            // ==== TEMPLATE UPLOAD ====
            $bgDataUri = $this->fileToDataUri($template->gambar_background);
            $ttdKepsekUri = $this->fileToDataUri($sertifikat->ttd_kepsek ?: $setting->ttd_kepsek);
            $ttdDudiUri = $this->fileToDataUri($sertifikat->ttd_dudi ?: $setting->ttd_dudi);

            $data = [
                'sertifikat'   => $sertifikat,
                'setting'      => $setting,
                'siswa'        => $sertifikat->siswa,
                'penempatan'   => $sertifikat->penempatanPkl,
                'nilai'        => $nilai,
                'qrDataUri'    => $qrDataUri,
                'verifyUrl'    => $verifyUrl,
                'template'     => $template,
                'bgDataUri'    => $bgDataUri,
                'ttdKepsekUri' => $ttdKepsekUri,
                'ttdDudiUri'   => $ttdDudiUri,
            ];

            $pdf = Pdf::loadView('pdf.sertifikat-template', $data)
                ->setPaper('a4', 'landscape');
        } else {
            // ==== FALLBACK: DESAIN DEFAULT ====
            $data = [
                'sertifikat' => $sertifikat,
                'setting'    => $setting,
                'siswa'      => $sertifikat->siswa,
                'penempatan' => $sertifikat->penempatanPkl,
                'nilai'      => $nilai,
                'qrDataUri'  => $qrDataUri,
                'verifyUrl'  => $verifyUrl,
            ];

            $pdf = Pdf::loadView('pdf.sertifikat', $data)
                ->setPaper('a4', 'landscape');
        }

        return $pdf->output();
    }

    /**
     * Nama file saat didownload.
     */
    public function namaFile(Sertifikat $sertifikat): string
    {
        $nama = $sertifikat->siswa->nama ?? 'Siswa';
        $nama = preg_replace('/[^A-Za-z0-9]+/', '-', $nama);
        return 'Sertifikat-PKL-' . trim($nama, '-') . '.pdf';
    }
}