<?php

namespace App\Filament\Pages;

use App\Models\Nilai;
use App\Models\Sertifikat;
use App\Models\Siswa;
use App\Services\HitungNilaiService;
use Filament\Pages\Page;

class NilaiSaya extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Nilai Saya';
    protected static ?string $title = 'Nilai PKL Saya';
    protected static ?string $navigationGroup = 'Penilaian PKL';
    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.nilai-saya';

    public ?array $nilaiData = null;
    public ?array $siswaData = null;
    public ?array $dudiData = null;
    public ?array $sertifikatData = null;

    public function mount(): void
    {
        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (! $siswa) {
            return;
        }

        $this->siswaData = [
            'nama'    => $siswa->nama,
            'nis'     => $siswa->nis,
            'nisn'    => $siswa->nisn,
            'kelas'   => $siswa->kelas,
            'jurusan' => $siswa->jurusan?->nama ?? '-',
        ];

        // ==== SERTIFIKAT (diambil by siswa_id, biar muncul walau nilai belum lengkap) ====
        $sertifikat = Sertifikat::where('siswa_id', $siswa->id)->first();
        if ($sertifikat) {
            $this->sertifikatData = [
                'id'             => $sertifikat->id,
                'nomor'          => $sertifikat->nomor_sertifikat,
                'status'         => $sertifikat->status,
                'status_label'   => $sertifikat->statusLabel(),
                'is_terbit'      => $sertifikat->isTerbit(),
                'is_ditolak'     => $sertifikat->status === Sertifikat::STATUS_DITOLAK,
                'tanggal_terbit' => $sertifikat->tanggal_terbit?->translatedFormat('d F Y'),
                'catatan_revisi' => $sertifikat->catatan_revisi,
                'download_url'   => route('sertifikat.download', $sertifikat),
            ];
        }

        // ==== NILAI ====
        $nilai = Nilai::with(['penempatanPkl.dudi', 'penempatanPkl.guruPembimbing', 'penempatanPkl.guruPenguji'])
            ->where('siswa_id', $siswa->id)
            ->first();

        if (! $nilai) {
            return;
        }

        $dudi = $nilai->penempatanPkl?->dudi;
        $this->dudiData = [
            'nama'       => $dudi?->nama_perusahaan ?? '-',
            'alamat'     => $dudi?->alamat ?? '-',
            'pembimbing' => $nilai->penempatanPkl?->guruPembimbing?->nama ?? '-',
            'penguji'    => $nilai->penempatanPkl?->guruPenguji?->nama ?? '-',
        ];

        $nilaiDudi = HitungNilaiService::rataRata([$nilai->nilai_kinerja, $nilai->nilai_kompetensi]);
        $nilaiGuru = HitungNilaiService::rataRata([$nilai->nilai_laporan, $nilai->nilai_presentasi]);

        $this->nilaiData = [
            'nilai_kinerja'    => $nilai->nilai_kinerja,
            'nilai_kompetensi' => $nilai->nilai_kompetensi,
            'nilai_laporan'    => $nilai->nilai_laporan,
            'nilai_presentasi' => $nilai->nilai_presentasi,
            'nilai_sidang'     => $nilai->nilai_sidang,
            'rata_dudi'        => $nilaiDudi,
            'rata_guru'        => $nilaiGuru,
            'nilai_akhir'      => $nilai->nilai_akhir,
            'predikat'         => HitungNilaiService::predikat(
                $nilai->nilai_akhir !== null ? (float) $nilai->nilai_akhir : null
            ),
            'catatan_dudi'     => $nilai->catatan_dudi,
            'catatan_guru'     => $nilai->catatan_guru,
            'catatan_penguji'  => $nilai->catatan_penguji,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('siswa') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('siswa') ?? false;
    }
}