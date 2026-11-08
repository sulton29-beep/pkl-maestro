<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanSertifikat extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_sertifikats';

    protected $fillable = [
        'nama_sekolah',
        'npsn',
        'alamat_sekolah',
        'judul_sertifikat',
        'nomor_format',
        'kalimat_pembuka',
        'kalimat_penutup',
        'ttd_kepsek',
        'nama_kepsek',
        'nip_kepsek',
        'ttd_dudi',
        'warna_utama',
    ];

    public static function getActive(): self
    {
        $setting = self::first();

        if (! $setting) {
            $setting = self::create([
                'nama_sekolah' => 'SMKS MAESTRO SEPATAN',
                'npsn' => '69876678',
                'alamat_sekolah' => 'Jl. Raya Kramat Pakuhaji Km 3,5, Kayu Agung, Kec. Sepatan, Tangerang',
                'judul_sertifikat' => 'SERTIFIKAT PRAKTIK KERJA LAPANGAN',
                'nomor_format' => '{no}/PKL/SMKS-MST/{tahun}',
                'kalimat_pembuka' => 'Kepala SMKS Maestro Sepatan dengan ini menerangkan bahwa siswa yang namanya tercantum di bawah ini telah melaksanakan Praktik Kerja Lapangan (PKL) dengan baik dan memenuhi semua ketentuan yang berlaku.',
                'kalimat_penutup' => 'Sertifikat ini diberikan sebagai bukti resmi telah menyelesaikan Praktik Kerja Lapangan. Demikian sertifikat ini dibuat untuk dipergunakan sebagaimana mestinya.',
                'warna_utama' => '#1e40af',
            ]);
        }

        return $setting;
    }
}